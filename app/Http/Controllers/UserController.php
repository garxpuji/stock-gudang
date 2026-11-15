<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter Status
        if ($request->filled('status')) {
            $status = $request->input('status') === 'active';
            $query->where('is_active', $status);
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();

        $totalUsers = User::count();
        $superadminCount = User::where('role', 'superadmin')->count();
        $adminGudangCount = User::where('role', 'admin_gudang')->count();
        $activeCount = User::where('is_active', true)->count();

        return view('users.index', compact(
            'users',
            'totalUsers',
            'superadminCount',
            'adminGudangCount',
            'activeCount'
        ));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin_gudang'])],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin_gudang'])],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Guard: Jika user sendiri, cegah non-aktifkan diri sendiri
        if (Auth::id() === $user->id && ! $request->boolean('is_active', true)) {
            return back()->withInput()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.');
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active', true),
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cek relasi transaksi penting
        if ($user->stockIns()->exists() || $user->stockOuts()->exists() || $user->returns()->exists()) {
            // Menonaktifkan alih-alih menghapus agar integritas audit log terjaga
            $user->update(['is_active' => false]);
            return redirect()->route('users.index')
                ->with('success', "Pengguna memiliki riwayat transaksi gudang. Status akun diubah menjadi Nonaktif.");
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus dari sistem.");
    }
}
