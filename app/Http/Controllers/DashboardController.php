<?php

namespace App\Http\Controllers;

use App\Enums\DamagedActionTaken;
use App\Enums\RecipientType;
use App\Models\Category;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\StockInTransaction;
use App\Models\StockMutation;
use App\Models\StockOutTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        // KPI Counts
        $totalProducts = Product::count();
        $totalActiveStockSum = Product::sum('stock');
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();
        $damagedStockCount = DamagedGood::pending()->count();

        // Monthly Flow Counts
        $monthlyInboundCount = StockInTransaction::where('transaction_date', '>=', $startOfMonth)->count();
        $monthlyOutboundTeknisiCount = StockOutTransaction::where('transaction_date', '>=', $startOfMonth)
            ->where('recipient_type', RecipientType::TEKNISI)
            ->count();
        $monthlyReturnsCount = ReturnTransaction::where('transaction_date', '>=', $startOfMonth)->count();

        // Urgent attention lists
        $criticalStockProducts = Product::with(['category', 'unit', 'location'])
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'min_stock')
                  ->orWhere('stock', '<=', 0);
            })
            ->orderBy('stock', 'asc')
            ->take(6)
            ->get();

        $pendingDamagedGoods = DamagedGood::with(['product.unit'])
            ->where('action_taken', DamagedActionTaken::PENDING)
            ->latest('reported_at')
            ->take(5)
            ->get();

        $recentDispatches = StockOutTransaction::with(['items.product', 'dispatchedBy'])
            ->latest('transaction_date')
            ->take(5)
            ->get();

        $recentMutations = StockMutation::with(['product.unit', 'systemOperator'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // Category breakdown
        $categoriesBreakdown = Category::withCount('products')
            ->with(['products' => function ($q) {
                $q->select('id', 'category_id', 'stock', 'damaged_stock');
            }])
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'code' => $cat->code,
                    'products_count' => $cat->products_count,
                    'total_stock' => $cat->products->sum('stock'),
                    'total_damaged' => $cat->products->sum('damaged_stock'),
                ];
            });

        return view('dashboard.index', compact(
            'totalProducts',
            'totalActiveStockSum',
            'lowStockCount',
            'outOfStockCount',
            'damagedStockCount',
            'monthlyInboundCount',
            'monthlyOutboundTeknisiCount',
            'monthlyReturnsCount',
            'criticalStockProducts',
            'pendingDamagedGoods',
            'recentDispatches',
            'recentMutations',
            'categoriesBreakdown'
        ));
    }
}
