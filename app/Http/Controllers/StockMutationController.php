<?php

namespace App\Http\Controllers;

use App\Enums\MutationType;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMutationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only([
            'search',
            'product_id',
            'mutation_type',
            'ticket_no',
            'person',
            'date_from',
            'date_to',
        ]);

        $query = StockMutation::with(['product.unit', 'systemOperator'])
            ->filter($filters);

        $mutations = $query->latest('created_at')->latest('id')->paginate(15)->withQueryString();

        $products = Product::orderBy('name')->get();
        $mutationTypes = MutationType::cases();

        return view('mutations.index', compact('mutations', 'products', 'mutationTypes', 'filters'));
    }
}
