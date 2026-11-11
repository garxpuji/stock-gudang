<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DamagedGoodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockMutationController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\StockOutController;
use Illuminate\Support\Facades\Route;

// Guest Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/quick-login', [AuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Warehouse Routes
Route::middleware(['web'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Master Data Barang
    Route::resource('products', ProductController::class);

    // Barang Masuk (Inbound / Pengadaan)
    Route::resource('stock-in', StockInController::class)->except(['edit', 'update', 'destroy']);

    // Barang Keluar (Outbound / Teknisi & Karyawan)
    Route::resource('stock-out', StockOutController::class)->except(['edit', 'update', 'destroy']);
    Route::get('stock-out/{stockOut}/print', [StockOutController::class, 'print'])->name('stock-out.print');

    // Barang Kembali (Returns / QC Inspection)
    Route::resource('returns', ReturnController::class)->except(['edit', 'update', 'destroy']);
    Route::get('returns/{return}/print', [ReturnController::class, 'print'])->name('returns.print');

    // Area Karantina & Barang Rusak
    Route::resource('damaged-goods', DamagedGoodController::class)->except(['edit', 'update', 'destroy']);
    Route::post('damaged-goods/{damagedGood}/resolve', [DamagedGoodController::class, 'resolve'])->name('damaged-goods.resolve');

    // Kartu Stok Terpadu (Unified Stock Ledger)
    Route::get('mutations', [StockMutationController::class, 'index'])->name('mutations.index');

    // Stok Opname
    Route::get('opname', [StockOpnameController::class, 'index'])->name('opname.index');
    Route::get('opname/create', [StockOpnameController::class, 'create'])->name('opname.create');
    Route::post('opname', [StockOpnameController::class, 'store'])->name('opname.store');
});
