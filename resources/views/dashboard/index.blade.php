@extends('layouts.app')

@section('title', 'Dashboard Logistik')

@section('content')
<div class="space-y-5">

    <!-- HEADER & AKSI CEPAT -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Dashboard Logistik &amp; Inventaris</h1>
            <p class="text-sm text-slate-600 mt-0.5">Ringkasan stok perangkat dan aktivitas serah terima harian gudang.</p>
        </div>

        <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2">
            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 min-h-[44px] rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm transition-colors focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span>Keluar Barang</span>
            </a>

            <a href="{{ route('stock-in.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 min-h-[44px] rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-sm transition-colors focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                <span>Barang Masuk</span>
            </a>

            <a href="{{ route('returns.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 min-h-[44px] rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-sm transition-colors focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M13 8l4-4m0 0l4 4m-4-4v12"></path>
                </svg>
                <span>Terima Retur</span>
            </a>

            <!-- Export Laporan Dropdown -->
            <div class="relative" x-data="{ exportOpen: false }" @keydown.escape.stop="exportOpen = false">
                <button type="button" @click="exportOpen = !exportOpen" @click.outside="exportOpen = false" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 min-h-[44px] w-full sm:w-auto rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-semibold text-sm transition-colors cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-4 h-4 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Unduh Laporan</span>
                </button>

                <div x-show="exportOpen" x-cloak x-transition class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-slate-200 py-2 z-30 divide-y divide-slate-100">
                    <div class="px-4 py-1.5 text-xs font-semibold text-slate-600">Pilih Laporan (Excel / PDF)</div>
                    <div class="p-2 space-y-1 text-xs">
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                            <span class="font-semibold text-slate-800">Stok Tersedia</span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('export.products', ['type' => 'excel']) }}" class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200 hover:bg-emerald-100">Excel</a>
                                <a href="{{ route('export.products', ['type' => 'pdf']) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-semibold border border-slate-200 hover:bg-slate-200">PDF</a>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                            <span class="font-semibold text-slate-800">Barang Keluar</span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('export.stock-out', ['type' => 'excel']) }}" class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200 hover:bg-emerald-100">Excel</a>
                                <a href="{{ route('export.stock-out', ['type' => 'pdf']) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-semibold border border-slate-200 hover:bg-slate-200">PDF</a>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                            <span class="font-semibold text-slate-800">Barang Kembali</span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('export.returns', ['type' => 'excel']) }}" class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200 hover:bg-emerald-100">Excel</a>
                                <a href="{{ route('export.returns', ['type' => 'pdf']) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-semibold border border-slate-200 hover:bg-slate-200">PDF</a>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                            <span class="font-semibold text-slate-800">Barang Rusak</span>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('export.damaged-goods', ['type' => 'excel']) }}" class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200 hover:bg-emerald-100">Excel</a>
                                <a href="{{ route('export.damaged-goods', ['type' => 'pdf']) }}" target="_blank" class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-semibold border border-slate-200 hover:bg-slate-200">PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KARTU INDIKATOR STOK UTAMA -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <a href="{{ route('products.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 hover:border-emerald-500 transition-colors focus:outline-hidden focus:ring-2 focus:ring-emerald-500">
            <span class="text-xs font-semibold text-slate-600 block">Total SKU</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ number_format($totalProducts) }}</span>
            <span class="text-xs text-slate-500 mt-1 block">Terdaftar di katalog</span>
        </a>

        <a href="{{ route('products.index', ['status' => 'LOW']) }}" class="bg-white p-4 rounded-xl border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200' }} hover:border-amber-500 transition-colors focus:outline-hidden focus:ring-2 focus:ring-amber-500">
            <span class="text-xs font-semibold text-amber-800 block">Stok Menipis</span>
            <span class="text-2xl font-bold text-amber-700 mt-1 block">{{ number_format($lowStockCount) }}</span>
            <span class="text-xs text-amber-800 mt-1 block">Di bawah batas minimum</span>
        </a>

        <a href="{{ route('products.index', ['status' => 'EMPTY']) }}" class="bg-white p-4 rounded-xl border {{ $outOfStockCount > 0 ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200' }} hover:border-rose-500 transition-colors focus:outline-hidden focus:ring-2 focus:ring-rose-500">
            <span class="text-xs font-semibold text-rose-800 block">Stok Habis</span>
            <span class="text-2xl font-bold text-rose-700 mt-1 block">{{ number_format($outOfStockCount) }}</span>
            <span class="text-xs text-rose-800 mt-1 block">Saldo fisik 0</span>
        </a>

        <a href="{{ route('damaged-goods.index') }}" class="bg-white p-4 rounded-xl border {{ $damagedStockCount > 0 ? 'border-orange-300 bg-orange-50/30' : 'border-slate-200' }} hover:border-orange-500 transition-colors focus:outline-hidden focus:ring-2 focus:ring-orange-500">
            <span class="text-xs font-semibold text-orange-800 block">Karantina Rusak</span>
            <span class="text-2xl font-bold text-orange-700 mt-1 block">{{ number_format($damagedStockCount) }}</span>
            <span class="text-xs text-orange-800 mt-1 block">Menunggu tindak lanjut</span>
        </a>
    </div>

    <!-- DUA KOLOM UTAMA: PERLU RESTOCK & PENGELUARAN TERAKHIR -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- PERLU RESTOCK -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h2 class="text-sm font-bold text-slate-900">Barang Perlu Diisi Ulang</h2>
                <a href="{{ route('products.index', ['status' => 'LOW']) }}" class="text-xs font-semibold text-emerald-700 hover:underline py-1">
                    Lihat Semua
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($criticalStockProducts as $prod)
                <a href="{{ route('products.show', $prod) }}" class="px-5 py-3 flex items-center justify-between hover:bg-slate-50 transition-colors gap-3 min-h-[56px]">
                    <div class="min-w-0 flex-1">
                        <span class="text-sm font-semibold text-slate-900 truncate block">{{ $prod->name }}</span>
                        <span class="text-xs text-slate-600 font-mono">{{ $prod->sku }} &bull; {{ $prod->location?->name ?? 'Gudang Utama' }}</span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold border {{ $prod->status_badge }}">
                            {{ $prod->formatted_stock }}
                        </span>
                        <span class="block text-[11px] text-slate-500 mt-0.5">Min: {{ number_format($prod->min_stock) }} {{ $prod->unit?->abbreviation }}</span>
                    </div>
                </a>
                @empty
                <div class="p-6 text-center text-sm text-slate-600">
                    Semua stok perangkat berada di atas batas minimum. Putar stok keluar atau tambah barang baru jika ada pengadaan.
                </div>
                @endforelse
            </div>
        </div>

        <!-- PENGELUARAN TERAKHIR -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="px-5 py-3.5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <h2 class="text-sm font-bold text-slate-900">Pengeluaran Barang Terakhir ({{ number_format($monthlyOutboundTeknisiCount) }} bulan ini)</h2>
                <a href="{{ route('stock-out.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline py-1">
                    Riwayat Keluar
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($recentDispatches as $disp)
                <div class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors min-h-[56px]">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('stock-out.show', $disp) }}" class="font-mono text-xs font-bold text-emerald-700 hover:underline">
                                {{ $disp->transaction_code }}
                            </a>
                            <span class="text-xs text-slate-500">{{ $disp->transaction_date->format('d/m H:i') }}</span>
                        </div>
                        <span class="text-sm font-semibold text-slate-900 truncate block">{{ $disp->picked_up_by }} ({{ $disp->recipient_department_or_team }})</span>
                        <span class="text-xs text-slate-600 truncate block">{{ $disp->purpose }}</span>
                    </div>

                    <a href="{{ route('stock-out.print', $disp) }}" target="_blank" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors shrink-0" title="Cetak Bukti Serah Terima" aria-label="Cetak Bukti Serah Terima {{ $disp->transaction_code }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                    </a>
                </div>
                @empty
                <div class="p-6 text-center text-sm text-slate-600">
                    Belum ada pengeluaran barang. Klik tombol <strong>Keluar Barang</strong> di atas untuk mencatat pengambilan teknisi.
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- RINGKASAN KATEGORI BARANG -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-slate-900">Stok Berdasarkan Kategori</h2>
            @if(Auth::user()?->isAdminGudang())
                <a href="{{ route('categories.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">Kelola Kategori</a>
            @endif
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            @foreach($categoriesBreakdown as $cat)
            <a href="{{ route('products.index', ['category_id' => $cat['id'] ?? '']) }}" class="p-3.5 rounded-xl bg-slate-50 hover:bg-emerald-50/60 border border-slate-200 hover:border-emerald-400 transition-colors flex flex-col justify-between min-h-[72px]">
                <div class="text-xs font-bold text-slate-900 truncate" title="{{ $cat['name'] }}">{{ $cat['name'] }}</div>
                <div class="mt-2 flex items-center justify-between text-xs pt-2 border-t border-slate-200">
                    <span class="text-slate-600">{{ $cat['products_count'] }} SKU</span>
                    <span class="font-bold text-emerald-800">{{ number_format($cat['total_stock'], 0) }} stok</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</div>
@endsection