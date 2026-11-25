<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Location::withCount('products');

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        $locations = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('master.locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('master.locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:locations,code'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $location = Location::create($validated);

        return redirect($this->resolveSafeRedirect($request->input('redirect_to'), 'locations.index'))
            ->with('success', "Lokasi Rak '{$location->name}' berhasil ditambahkan.");
    }

    public function edit(Location $location): View
    {
        return view('master.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('locations', 'code')->ignore($location->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')
            ->with('success', "Lokasi Rak '{$location->name}' berhasil diperbarui.");
    }

    public function destroy(Location $location): RedirectResponse
    {
        if ($location->products()->exists()) {
            return back()->with('error', "Lokasi Rak '{$location->name}' tidak dapat dihapus karena masih digunakan oleh produk di katalog.");
        }

        $name = $location->name;
        $location->delete();

        return back()->with('success', "Lokasi Rak '{$name}' berhasil dihapus.");
    }
}
