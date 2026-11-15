<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Category::withCount('products');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('master.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('master.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:categories,code'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category = Category::create($validated);

        if ($request->filled('redirect_to')) {
            $redirectTo = (string) $request->input('redirect_to');
            if ((str_starts_with($redirectTo, '/') && ! str_starts_with($redirectTo, '//')) || str_starts_with($redirectTo, url('/'))) {
                return redirect($redirectTo)
                    ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
            }
        }

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    public function edit(Category $category): View
    {
        return view('master.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('categories', 'code')->ignore($category->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh produk di katalog.");
        }

        $name = $category->name;
        $category->delete();

        return back()->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
