<?php

namespace App\Providers;

use App\Models\DamagedGood;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('layouts.navigation-links', function (\Illuminate\View\View $view): void {
            $view->with([
                'pendingDmg' => once(fn (): int => DamagedGood::where('action_taken', 'PENDING')->count()),
                'totalUserCount' => once(fn (): int => User::count()),
            ]);
        });
    }
}
