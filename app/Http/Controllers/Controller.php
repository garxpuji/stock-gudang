<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidSerialException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    /**
     * Handle exceptions during transactional actions without leaking internal SQL/system messages.
     */
    protected function handleTransactionException(\Throwable $e, string $context, bool $withInput = true): RedirectResponse
    {
        if (
            $e instanceof InsufficientStockException
            || $e instanceof InvalidSerialException
            || $e instanceof \DomainException
        ) {
            return $withInput
                ? back()->withInput()->with('error', $e->getMessage())
                : back()->with('error', $e->getMessage());
        }

        Log::error($context, [
            'exception' => $e::class,
            'message' => $e->getMessage(),
            'user_id' => Auth::id(),
        ]);

        $genericMessage = 'Terjadi kesalahan sistem saat memproses transaksi. Silakan coba lagi atau hubungi Administrator.';

        return $withInput
            ? back()->withInput()->with('error', $genericMessage)
            : back()->with('error', $genericMessage);
    }

    /**
     * Validate redirect_to against an explicit whitelist of internal routes.
     */
    protected function resolveSafeRedirect(?string $redirectTo, string $fallbackRoute): string
    {
        $whitelistedRoutes = [
            'products.create',
            'products.index',
            'stock-in.create',
            'categories.index',
            'units.index',
            'locations.index',
            'suppliers.index',
        ];

        $allowedTargets = [];
        foreach ($whitelistedRoutes as $routeName) {
            $allowedTargets[] = route($routeName);
            $allowedTargets[] = route($routeName, [], false);
        }

        if (is_string($redirectTo) && $redirectTo !== '' && in_array($redirectTo, $allowedTargets, true)) {
            return $redirectTo;
        }

        return route($fallbackRoute);
    }
}
