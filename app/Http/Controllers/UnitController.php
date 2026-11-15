<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $query = Unit::withCount('products');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('master.units.index', compact('units'));
    }

    public function create(): View
    {
        return view('master.units.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:20', 'unique:units,abbreviation'],
        ]);

        $unit = Unit::create($validated);

        if ($request->filled('redirect_to')) {
            return redirect($request->input('redirect_to'))
                ->with('success', "Satuan '{$unit->name} ({$unit->abbreviation})' berhasil ditambahkan.");
        }

        return redirect()->route('units.index')
            ->with('success', "Satuan '{$unit->name} ({$unit->abbreviation})' berhasil ditambahkan.");
    }

    public function edit(Unit $unit): View
    {
        return view('master.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'abbreviation' => ['required', 'string', 'max:20', Rule::unique('units', 'abbreviation')->ignore($unit->id)],
        ]);

        $unit->update($validated);

        return redirect()->route('units.index')
            ->with('success', "Satuan '{$unit->name}' berhasil diperbarui.");
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->products()->exists()) {
            return back()->with('error', "Satuan '{$unit->name}' tidak dapat dihapus karena masih digunakan oleh produk di katalog.");
        }

        $name = $unit->name;
        $unit->delete();

        return back()->with('success', "Satuan '{$name}' berhasil dihapus.");
    }
}
