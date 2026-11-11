<?php

namespace App\Http\Controllers;

use App\Actions\OutboundStockAction;
use App\Enums\RecipientType;
use App\Http\Requests\StoreOutboundRequest;
use App\Models\Product;
use App\Models\StockOutTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockOutController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockOutTransaction::with(['dispatchedBy', 'items.product.unit', 'items.serials']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhere('picked_up_by', 'like', "%{$search}%")
                  ->orWhere('reference_ticket_no', 'like', "%{$search}%")
                  ->orWhere('recipient_department_or_team', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
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

        return view('stock_out.index', compact('transactions'));
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
        $userId = Auth::id() ?? 1;

        try {
            $transaction = $action->execute($request->validated(), $userId);

            return redirect()->route('stock-out.show', $transaction)
                ->with('success', "Pengeluaran barang [{$transaction->transaction_code}] berhasil dibukukan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(StockOutTransaction $stockOut): View
    {
        $stockOut->load(['dispatchedBy', 'items.product.unit', 'items.serials']);

        return view('stock_out.show', compact('stockOut'));
    }

    public function print(StockOutTransaction $stockOut): View
    {
        $stockOut->load(['dispatchedBy', 'items.product.unit', 'items.serials']);

        return view('stock_out.print', compact('stockOut'));
    }
}
