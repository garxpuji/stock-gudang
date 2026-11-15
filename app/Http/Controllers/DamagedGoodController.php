<?php

namespace App\Http\Controllers;

use App\Actions\RecordDamagedGoodsAction;
use App\Actions\ResolveDamagedGoodsAction;
use App\Enums\DamagedActionTaken;
use App\Http\Requests\ResolveDamagedGoodRequest;
use App\Http\Requests\StoreDamagedGoodRequest;
use App\Models\DamagedGood;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DamagedGoodController extends Controller
{
    public function index(Request $request): View
    {
        $query = DamagedGood::with(['product.unit', 'resolvedBy']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%")
                    ->orWhere('reported_by', 'like', "%{$search}%")
                    ->orWhere('damage_description', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action_taken')) {
            if ($request->input('action_taken') === 'PENDING') {
                $query->where('action_taken', DamagedActionTaken::PENDING);
            } elseif ($request->input('action_taken') === 'RESOLVED') {
                $query->where('action_taken', '!=', DamagedActionTaken::PENDING);
            } else {
                $query->where('action_taken', $request->input('action_taken'));
            }
        }

        if ($request->filled('category')) {
            $query->where('damage_category', $request->input('category'));
        }

        $damagedGoods = $query->latest('reported_at')->paginate(12)->withQueryString();

        $pendingCount = DamagedGood::where('action_taken', DamagedActionTaken::PENDING)->count();
        $rmaCount = DamagedGood::where('action_taken', DamagedActionTaken::RMA_VENDOR)->count();
        $scrappedCount = DamagedGood::where('action_taken', DamagedActionTaken::SCRAPPED)->count();
        $repairedCount = DamagedGood::where('action_taken', DamagedActionTaken::REPAIRED_INTERNAL)->count();

        return view('damaged_goods.index', compact(
            'damagedGoods',
            'pendingCount',
            'rmaCount',
            'scrappedCount',
            'repairedCount'
        ));
    }

    public function create(): View
    {
        $products = Product::with(['unit', 'category'])->orderBy('name')->get();

        return view('damaged_goods.create', compact('products'));
    }

    public function store(StoreDamagedGoodRequest $request, RecordDamagedGoodsAction $action): RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        try {
            $data = $request->validated();

            if ($request->hasFile('photo_proof')) {
                $data['photo_proof'] = $request->file('photo_proof')->store('damaged_proofs', 'public');
            }

            $damagedGood = $action->execute($data, $userId);

            return redirect()->route('damaged-goods.show', $damagedGood)
                ->with('success', "Barang rusak [{$damagedGood->code}] berhasil dicatat dan masuk ke Area Karantina.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(DamagedGood $damagedGood): View
    {
        $damagedGood->load(['product.unit', 'product.category', 'returnItem.transaction', 'resolvedBy']);

        return view('damaged_goods.show', compact('damagedGood'));
    }

    public function resolve(ResolveDamagedGoodRequest $request, DamagedGood $damagedGood, ResolveDamagedGoodsAction $action): RedirectResponse
    {
        $userId = Auth::id() ?? 1;

        try {
            $resolved = $action->execute(
                $damagedGood->id,
                $request->input('action_taken'),
                $request->only(['action_notes', 'loss_cost', 'rma_number']),
                $userId
            );

            return redirect()->route('damaged-goods.show', $resolved)
                ->with('success', "Tindakan lanjutan untuk [{$resolved->code}] berhasil diproses: {$resolved->action_taken->label()}.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
