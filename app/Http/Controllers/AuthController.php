<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang di Sistem Logistik & Inventaris Gudang Gayuh Media Informatika.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data petugas gudang.',
        ])->onlyInput('email');
    }

    public function quickLogin(Request $request): RedirectResponse
    {
        $email = $request->input('email', 'admin@gayuhmedia.id');
        $user = User::where('email', $email)->first();

        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', "Masuk sebagai {$user->name} ({$user->role})");
        }

        return redirect()->route('login');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem gudang.');
    }
}
