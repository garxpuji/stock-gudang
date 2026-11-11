<?php

namespace App\Http\Controllers;

use App\Actions\StockOpnameAction;
use App\Http\Requests\StockOpnameRequest;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StockOpnameController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'unit', 'location'])->orderBy('name')->get();

        return view('opname.index', compact('products'));
    }

    public function create(Request $request): View
    {
        $products = Product::with(['category', 'unit', 'location'])->orderBy('name')->get();
        $selectedProduct = null;

        if ($request->filled('product_id')) {
            $selectedProduct = Product::find($request->input('product_id'));
        }

        return view('opname.create', compact('products', 'selectedProduct'));
    }

    public function store(StockOpnameRequest $request, StockOpnameAction $action): RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        try {
            $product = $action->execute($request->validated(), $userId);

            return redirect()->route('products.show', $product)
                ->with('success', "Penyesuaian stok opname untuk '{$product->name}' berhasil disimpan.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
