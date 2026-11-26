<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\SqlLike;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    private function authorizeAdminAccess(): void
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'Akses ditolak. Hanya Superadmin yang dapat mengelola pengguna.');
    }

    private function revokeUserSessions(User $targetUser): void
    {
        $targetUser->forceFill(['remember_token' => Str::random(60)])->save();

        $table = (string) config('session.table', 'sessions');
        $connection = config('session.connection');

        if (Schema::connection($connection)->hasTable($table)) {
            DB::connection($connection)
                ->table($table)
                ->where('user_id', $targetUser->id)
                ->delete();
        }
    }

    public function index(Request $request): View
    {
        $this->authorizeAdminAccess();

        $query = User::query();

        if ($request->filled('search')) {
            $like = SqlLike::contains((string) $request->input('search'));
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

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
        $this->authorizeAdminAccess();

        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdminAccess();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin_gudang', 'staff_gudang'])],
            'password' => ['required', 'string', 'max:128', Password::min(10)->letters()->numbers(), 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'current_password' => ['required', 'string', 'current_password'],
        ], [
            'current_password.required' => 'Kata sandi Superadmin Anda wajib diisi untuk menambah pengguna.',
            'current_password.current_password' => 'Kata sandi Superadmin yang Anda masukkan tidak sesuai.',
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);
        Cache::forget('nav.total_users_count');

        Log::info('security.user_created', [
            'actor_id' => Auth::id(),
            'target_user_id' => $newUser->id,
            'role' => $newUser->role,
            'is_active' => $newUser->is_active,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$validated['name']} berhasil ditambahkan.");
    }

    public function edit(User $user): View
    {
        $this->authorizeAdminAccess();

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAdminAccess();

        $isSensitiveChange = ($request->input('role') !== $user->role)
            || $request->filled('password')
            || ($request->boolean('is_active', true) !== (bool) $user->is_active);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25'],
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin_gudang', 'staff_gudang'])],
            'password' => ['nullable', 'string', 'max:128', Password::min(10)->letters()->numbers(), 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'current_password' => [Rule::requiredIf($isSensitiveChange), 'nullable', 'string', 'current_password'],
        ], [
            'current_password.required' => 'Kata sandi Superadmin Anda wajib diisi saat mengubah peran, status aktif, atau kata sandi pengguna.',
            'current_password.current_password' => 'Kata sandi Superadmin yang Anda masukkan tidak sesuai.',
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

        if ($isSensitiveChange) {
            if (Auth::id() === $user->id) {
                $request->session()->regenerate();
                $user->forceFill(['remember_token' => Str::random(60)])->save();
            } else {
                $this->revokeUserSessions($user);
            }
        }

        Log::info('security.user_updated', [
            'actor_id' => Auth::id(),
            'target_user_id' => $user->id,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'password_changed' => ! empty($validated['password']),
        ]);

        return redirect()->route('users.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeAdminAccess();

        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Cek seluruh relasi transaksi & audit log
        if (
            $user->stockIns()->exists()
            || $user->stockOuts()->exists()
            || $user->returns()->exists()
            || $user->mutations()->exists()
            || $user->resolvedDamagedGoods()->exists()
        ) {
            // Menonaktifkan alih-alih menghapus agar integritas audit log terjaga
            $user->update(['is_active' => false]);
            $this->revokeUserSessions($user);

            Log::info('security.user_deactivated', [
                'actor_id' => Auth::id(),
                'target_user_id' => $user->id,
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna memiliki riwayat transaksi gudang. Status akun diubah menjadi Nonaktif.');
        }

        $userId = $user->id;
        $userName = $user->name;
        $this->revokeUserSessions($user);
        $user->delete();
        Cache::forget('nav.total_users_count');

        Log::info('security.user_deleted', [
            'actor_id' => Auth::id(),
            'target_user_id' => $userId,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Pengguna {$userName} berhasil dihapus dari sistem.");
    }
}
