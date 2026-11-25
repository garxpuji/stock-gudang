<?php

namespace App\Providers;

use App\Models\DamagedGood;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request): array {
            $email = Str::transliterate(Str::lower(trim((string) $request->input('email', ''))));

            return [
                Limit::perMinute(5)->by($email.'|'.$request->ip()),
                Limit::perMinute(20)->by('ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('exports', fn (Request $request): Limit => Limit::perMinute(10)->by((string) ($request->user()?->id ?: $request->ip())));

        View::composer('layouts.navigation-links', function (\Illuminate\View\View $view): void {
            $view->with([
                'pendingDmg' => once(fn (): int => DamagedGood::where('action_taken', 'PENDING')->count()),
                'totalUserCount' => once(fn (): int => Auth::user()?->isSuperAdmin() ? User::count() : 0),
            ]);
        });
    }
}
