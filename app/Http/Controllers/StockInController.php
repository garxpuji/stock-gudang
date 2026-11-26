<?php

namespace App\Http\Controllers;

use App\Actions\InboundStockAction;
use App\Http\Requests\StoreInboundRequest;
use App\Models\Product;
use App\Models\StockInTransaction;
use App\Models\Supplier;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockInController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockInTransaction::with(['supplier', 'receivedBy']);

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('transaction_code', 'like', $like)
                    ->orWhere('po_number', 'like', $like)
                    ->orWhere('vendor_delivery_order_no', 'like', $like)
                    ->orWhere('supplier_name', 'like', $like)
                    ->orWhere('courier_or_driver_name', 'like', $like);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $transactions = $query->latest('transaction_date')->latest('id')->paginate(10)->withQueryString();

        return view('stock_in.index', compact('transactions'));
    }

    public function create(): View
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::with(['unit', 'category'])->orderBy('name')->get();

        return view('stock_in.create', compact('suppliers', 'products'));
    }

    public function store(StoreInboundRequest $request, InboundStockAction $action): RedirectResponse
    {
        $userId = Auth::id();
        abort_if($userId === null, 401);

        try {
            $transaction = $action->execute($request->validated(), $userId);

            return redirect()->route('stock-in.show', $transaction)
                ->with('success', "Penerimaan barang masuk [{$transaction->transaction_code}] berhasil diproses.");
        } catch (\Throwable $e) {
            return $this->handleTransactionException($e, 'stock_in.store_failed');
        }
    }

    public function show(StockInTransaction $stockIn): View
    {
        $stockIn->load(['supplier', 'receivedBy', 'items.product.unit']);

        return view('stock_in.show', compact('stockIn'));
    }
}
