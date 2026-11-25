<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::withCount('stockIns');

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('contact_name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        $suppliers = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('master.suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('master.suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:suppliers,code'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier = Supplier::create($validated);

        return redirect($this->resolveSafeRedirect($request->input('redirect_to'), 'suppliers.index'))
            ->with('success', "Supplier '{$supplier->name}' berhasil ditambahkan.");
    }

    public function edit(Supplier $supplier): View
    {
        return view('master.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('suppliers', 'code')->ignore($supplier->id)],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', "Data supplier '{$supplier->name}' berhasil diperbarui.");
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->stockIns()->exists()) {
            return back()->with('error', "Supplier '{$supplier->name}' tidak dapat dihapus karena memiliki riwayat transaksi barang masuk.");
        }

        $name = $supplier->name;
        $supplier->delete();

        return back()->with('success', "Supplier '{$name}' berhasil dihapus.");
    }
}
