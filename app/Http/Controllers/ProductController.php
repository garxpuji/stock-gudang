<?php

namespace App\Http\Controllers;

use App\Enums\MutationType;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'unit', 'location'])
            ->withCount([
                'serials as available_serials_count' => fn($q) => $q->where('status', 'AVAILABLE'),
                'serials as issued_serials_count' => fn($q) => $q->where('status', 'ISSUED'),
                'serials as damaged_serials_count' => fn($q) => $q->where('status', 'DAMAGED'),
            ]);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id'));
        }

        if ($request->filled('status')) {
            match ($request->input('status')) {
                'LOW' => $query->lowStock(),
                'EMPTY' => $query->outOfStock(),
                'SAFE' => $query->whereColumn('stock', '>', 'min_stock'),
                'DAMAGED' => $query->hasDamaged(),
                default => null,
            };
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'locations'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'unit', 'location', 'serials']);

        $recentMutations = $product->mutations()
            ->with('systemOperator')
            ->take(15)
            ->get();

        $damagedRecords = $product->damagedGoods()
            ->latest('reported_at')
            ->take(10)
            ->get();

        return view('products.show', compact('product', 'recentMutations', 'damagedRecords'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('products.create', compact('categories', 'units', 'locations'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($request->validated());

            $initialStock = (float) $product->stock;
            $initialDamaged = (float) $product->damaged_stock;

            if ($initialStock > 0 || $initialDamaged > 0) {
                StockMutation::create([
                    'product_id' => $product->id,
                    'mutation_type' => MutationType::OPNAME_ADJUSTMENT,
                    'qty' => $initialStock,
                    'previous_stock' => 0,
                    'current_stock' => $initialStock,
                    'previous_damaged_stock' => 0,
                    'current_damaged_stock' => $initialDamaged,
                    'system_operator_id' => Auth::id(),
                    'person_in_charge' => Auth::user()?->name ?? 'Admin Gudang',
                    'reference_table' => 'products',
                    'reference_id' => $product->id,
                    'remarks' => "Saldo awal registrasi katalog produk [{$product->sku}]",
                ]);
            }

            return $product;
        });

        return redirect()->route('products.show', $product)
            ->with('success', "Perangkat/Material '{$product->name}' berhasil ditambahkan ke katalog gudang.");
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        $units = Unit::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'units', 'locations'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.show', $product)
            ->with('success', "Data '{$product->name}' berhasil diperbarui.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Guard: jika produk memiliki riwayat mutasi / transaksi, cegah penghapusan
        if (
            $product->mutations()->exists()
            || $product->serials()->exists()
            || $product->stockInItems()->exists()
            || $product->stockOutItems()->exists()
            || $product->returnItems()->exists()
            || $product->damagedGoods()->exists()
        ) {
            return back()->with('error', "Perangkat/Material '{$product->name}' tidak dapat dihapus karena sudah memiliki riwayat mutasi stok atau serial number.");
        }

        $name = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', "Perangkat/Material '{$name}' berhasil dihapus dari katalog.");
    }
}
