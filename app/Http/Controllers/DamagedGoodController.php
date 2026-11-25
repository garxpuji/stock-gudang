<?php

namespace App\Http\Controllers;

use App\Actions\RecordDamagedGoodsAction;
use App\Actions\ResolveDamagedGoodsAction;
use App\Enums\DamagedActionTaken;
use App\Http\Requests\ResolveDamagedGoodRequest;
use App\Http\Requests\StoreDamagedGoodRequest;
use App\Models\DamagedGood;
use App\Models\Product;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DamagedGoodController extends Controller
{
    public function index(Request $request): View
    {
        $query = DamagedGood::with(['product.unit', 'resolvedBy']);

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('code', 'like', $like)
                    ->orWhere('serial_number', 'like', $like)
                    ->orWhere('reported_by', 'like', $like)
                    ->orWhere('damage_description', 'like', $like)
                    ->orWhereHas('product', function ($pq) use ($like) {
                        $pq->where('name', 'like', $like)
                            ->orWhere('sku', 'like', $like);
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
        $userId = Auth::id();
        abort_if($userId === null, 401);

        $storedPath = null;

        try {
            $data = $request->validated();

            if ($request->hasFile('photo_proof')) {
                $storedPath = $request->file('photo_proof')->store('damaged_proofs', 'local');
                $data['photo_proof'] = $storedPath;
            }

            $damagedGood = $action->execute($data, $userId);

            return redirect()->route('damaged-goods.show', $damagedGood)
                ->with('success', "Barang rusak [{$damagedGood->code}] berhasil dicatat dan masuk ke Area Karantina.");
        } catch (\Throwable $e) {
            if ($storedPath !== null) {
                Storage::disk('local')->delete($storedPath);
            }

            return $this->handleTransactionException($e, 'damaged_goods.store_failed');
        }
    }

    public function show(DamagedGood $damagedGood): View
    {
        $damagedGood->load(['product.unit', 'product.category', 'returnItem.transaction', 'resolvedBy']);

        return view('damaged_goods.show', compact('damagedGood'));
    }

    public function photo(DamagedGood $damagedGood): StreamedResponse
    {
        $path = $damagedGood->photo_proof;

        abort_if(
            ! is_string($path)
            || $path === ''
            || str_contains($path, '..')
            || ! str_starts_with($path, 'damaged_proofs/'),
            404
        );

        if (Storage::disk('local')->exists($path)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($path)) {
            $disk = 'public';
        } else {
            abort(404);
        }

        return Storage::disk($disk)->response($path, null, [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function resolve(ResolveDamagedGoodRequest $request, DamagedGood $damagedGood, ResolveDamagedGoodsAction $action): RedirectResponse
    {
        $userId = Auth::id();
        abort_if($userId === null, 401);

        try {
            $resolved = $action->execute(
                $damagedGood->id,
                $request->input('action_taken'),
                $request->only(['action_notes', 'loss_cost', 'rma_number']),
                $userId
            );

            return redirect()->route('damaged-goods.show', $resolved)
                ->with('success', "Tindakan lanjutan untuk [{$resolved->code}] berhasil diproses: {$resolved->action_taken->label()}.");
        } catch (\Throwable $e) {
            return $this->handleTransactionException($e, 'damaged_goods.resolve_failed', false);
        }
    }
}
