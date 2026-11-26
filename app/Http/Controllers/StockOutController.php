<?php

namespace App\Http\Controllers;

use App\Actions\OutboundStockAction;
use App\Http\Requests\StoreOutboundRequest;
use App\Models\Product;
use App\Models\StockOutTransaction;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockOutController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockOutTransaction::with(['dispatchedBy']);

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('transaction_code', 'like', $like)
                    ->orWhere('picked_up_by', 'like', $like)
                    ->orWhere('reference_ticket_no', 'like', $like)
                    ->orWhere('recipient_department_or_team', 'like', $like)
                    ->orWhere('purpose', 'like', $like);
            });
        }

        if ($request->filled('technician')) {
            $query->where('picked_up_by', $request->input('technician'));
        }

        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->input('recipient_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        $technicians = StockOutTransaction::whereNotNull('picked_up_by')
            ->where('picked_up_by', '!=', '')
            ->distinct()
            ->orderBy('picked_up_by')
            ->pluck('picked_up_by');

        return view('stock_out.index', compact('transactions', 'technicians'));
    }

    public function create(): View
    {
        $products = Product::with(['unit', 'category', 'availableSerials'])
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('stock_out.create', compact('products'));
    }

    public function store(StoreOutboundRequest $request, OutboundStockAction $action): RedirectResponse
    {
        $userId = Auth::id();
        abort_if($userId === null, 401);

        try {
            $transaction = $action->execute($request->validated(), $userId);

            return redirect()->route('stock-out.show', $transaction)
                ->with('success', "Pengeluaran barang [{$transaction->transaction_code}] berhasil dibukukan.");
        } catch (\Throwable $e) {
            return $this->handleTransactionException($e, 'stock_out.store_failed');
        }
    }

    public function show(StockOutTransaction $stockOut): View
    {
        $stockOut->load(['dispatchedBy', 'items.product.unit', 'items.product.category', 'items.serials']);

        return view('stock_out.show', compact('stockOut'));
    }

    public function print(StockOutTransaction $stockOut): View
    {
        $stockOut->load(['dispatchedBy', 'items.product.unit', 'items.product.category', 'items.serials']);

        return view('stock_out.print', compact('stockOut'));
    }
}
