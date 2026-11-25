<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();

            Log::info('security.login_succeeded', [
                'user_id' => Auth::id(),
                'email' => $credentials['email'],
                'ip' => $request->ip(),
            ]);

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang di Sistem Logistik & Inventaris Gudang Gayuh Media Informatika.');
        }

        Log::warning('security.login_failed', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
        ]);

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok, atau akun petugas gudang sedang dinonaktifkan.',
        ])->onlyInput('email');
    }

    public function quickLogin(Request $request): RedirectResponse
    {
        abort_unless(app()->environment(['local', 'testing']), 403);

        $validated = $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        $email = $validated['email'] ?? 'admin@gayuhmedia.id';
        $user = User::where('email', $email)->where('is_active', true)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();

            Log::info('security.quick_login', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('dashboard')->with('success', "Masuk sebagai {$user->name} ({$user->role})");
        }

        return redirect()->route('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('security.logout', [
            'user_id' => $userId,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem gudang.');
    }
}
