<?php

namespace App\Http\Controllers;

use App\Actions\ProcessReturnAction;
use App\Enums\DamageCategory;
use App\Enums\QcStatus;
use App\Enums\RecipientType;
use App\Http\Requests\StoreReturnRequest;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\StockOutTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        $query = ReturnTransaction::with(['receivedBy', 'items.product.unit']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('returned_by', 'like', "%{$search}%")
                  ->orWhere('reference_ticket_no', 'like', "%{$search}%")
                  ->orWhere('return_category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $returns = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        return view('returns.index', compact('returns'));
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
        $userId = Auth::id() ?? 1;

        try {
            $data = $request->validated();

            // Handle optional photo proof upload per item if provided
            if ($request->hasFile('photo_proof')) {
                $path = $request->file('photo_proof')->store('damaged_proofs', 'public');
                $data['items'][0]['photo_proof'] = $path;
            }

            $transaction = $action->execute($data, $userId);

            return redirect()->route('returns.show', $transaction)
                ->with('success', "Penerimaan retur barang [{$transaction->transaction_code}] dengan inspeksi QC berhasil disimpan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
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
