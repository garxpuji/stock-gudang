<?php

namespace App\Http\Controllers;

use App\Actions\ProcessReturnAction;
use App\Enums\QcStatus;
use App\Http\Requests\StoreReturnRequest;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\StockOutTransaction;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        $query = ReturnTransaction::with(['receivedBy', 'items:id,return_transaction_id,qc_status']);

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('transaction_code', 'like', $like)
                    ->orWhere('returned_by', 'like', $like)
                    ->orWhere('reference_ticket_no', 'like', $like)
                    ->orWhere('return_category', 'like', $like);
            });
        }

        if ($request->filled('technician')) {
            $query->where('returned_by', $request->input('technician'));
        }

        if ($request->filled('qc_status')) {
            $query->whereHas('items', function ($iq) use ($request) {
                $iq->where('qc_status', $request->input('qc_status'));
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $returns = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        $technicians = ReturnTransaction::whereNotNull('returned_by')
            ->where('returned_by', '!=', '')
            ->distinct()
            ->orderBy('returned_by')
            ->pluck('returned_by');

        $qcStatuses = QcStatus::cases();

        return view('returns.index', compact('returns', 'technicians', 'qcStatuses'));
    }

    public function create(Request $request): View
    {
        $products = Product::with(['unit', 'category'])->orderBy('name')->get();
        $recentOutbounds = StockOutTransaction::latest('transaction_date')->take(20)->get();

        // Optional pre-select from outbound reference
        $selectedOutbound = null;
        if ($request->filled('outbound_id')) {
            $selectedOutbound = StockOutTransaction::with('items.product')->find($request->input('outbound_id'));
        }

        return view('returns.create', compact('products', 'recentOutbounds', 'selectedOutbound'));
    }

    public function store(StoreReturnRequest $request, ProcessReturnAction $action): RedirectResponse
    {
        $userId = Auth::id();
        abort_if($userId === null, 401);

        $storedFiles = [];

        try {
            $data = $request->validated();

            // Handle optional photo proof upload per item if provided
            foreach ($data['items'] as $idx => $item) {
                if ($request->hasFile("items.{$idx}.photo_proof")) {
                    $path = $request->file("items.{$idx}.photo_proof")->store('damaged_proofs', 'local');
                    $storedFiles[] = $path;
                    $data['items'][$idx]['photo_proof'] = $path;
                }
            }

            if ($request->hasFile('photo_proof') && empty($data['items'][0]['photo_proof'])) {
                $path = $request->file('photo_proof')->store('damaged_proofs', 'local');
                $storedFiles[] = $path;
                $data['items'][0]['photo_proof'] = $path;
            }

            $transaction = $action->execute($data, $userId);
            Cache::forget('nav.pending_damaged_count');

            return redirect()->route('returns.show', $transaction)
                ->with('success', "Penerimaan retur barang [{$transaction->transaction_code}] dengan inspeksi QC berhasil disimpan.");
        } catch (\Throwable $e) {
            if ($storedFiles !== []) {
                Storage::disk('local')->delete($storedFiles);
            }

            return $this->handleTransactionException($e, 'returns.store_failed');
        }
    }

    public function show(ReturnTransaction $return): View
    {
        $return->load(['receivedBy', 'originalStockOut', 'items.product.unit', 'items.damagedGoodRecord']);

        return view('returns.show', compact('return'));
    }

    public function print(ReturnTransaction $return): View
    {
        $return->load(['receivedBy', 'originalStockOut', 'items.product.unit', 'items.damagedGoodRecord']);

        return view('returns.print', compact('return'));
    }
}
