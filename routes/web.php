<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DamagedGoodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockMutationController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Guest Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/quick-login', [AuthController::class, 'quickLogin'])->middleware('throttle:login')->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Warehouse Routes
Route::middleware(['web', 'auth', 'active'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Manajemen Pengguna (Khusus Superadmin)
    Route::middleware('role:superadmin')->group(function () {
        Route::resource('users', UserController::class);
    });

    // Administrasi Gudang, Master Data & Rekonsiliasi (Superadmin & Admin Gudang)
    Route::middleware('role:superadmin,admin_gudang')->group(function () {
        // Daftarkan rute write/create sebelum {product} agar /products/create tidak bentrok
        Route::resource('products', ProductController::class)->except(['index', 'show']);

        Route::get('opname', [StockOpnameController::class, 'index'])->name('opname.index');
        Route::get('opname/create', [StockOpnameController::class, 'create'])->name('opname.create');
        Route::post('opname', [StockOpnameController::class, 'store'])->name('opname.store');

        Route::post('damaged-goods/{damagedGood}/resolve', [DamagedGoodController::class, 'resolve'])->name('damaged-goods.resolve');

        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('units', UnitController::class)->except(['show']);
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
    });

    // Operasional Harian Gudang (Superadmin, Admin Gudang & Staff Gudang)
    Route::middleware('role:superadmin,admin_gudang,staff_gudang')->group(function () {
        Route::resource('products', ProductController::class)->only(['index', 'show']);

        Route::resource('stock-in', StockInController::class)->except(['edit', 'update', 'destroy']);

        Route::resource('stock-out', StockOutController::class)->except(['edit', 'update', 'destroy']);
        Route::get('stock-out/{stockOut}/print', [StockOutController::class, 'print'])->name('stock-out.print');

        Route::resource('returns', ReturnController::class)->except(['edit', 'update', 'destroy']);
        Route::get('returns/{return}/print', [ReturnController::class, 'print'])->name('returns.print');

        Route::get('damaged-goods/{damagedGood}/photo', [DamagedGoodController::class, 'photo'])->name('damaged-goods.photo');
        Route::resource('damaged-goods', DamagedGoodController::class)->except(['edit', 'update', 'destroy']);

        Route::get('mutations', [StockMutationController::class, 'index'])->name('mutations.index');

        Route::prefix('export')->name('export.')->middleware('throttle:exports')->group(function () {
            Route::get('/products', [ExportController::class, 'exportProducts'])->name('products');
            Route::get('/stock-out', [ExportController::class, 'exportStockOut'])->name('stock-out');
            Route::get('/returns', [ExportController::class, 'exportReturns'])->name('returns');
            Route::get('/damaged-goods', [ExportController::class, 'exportDamagedGoods'])->name('damaged-goods');
        });
    });
});
