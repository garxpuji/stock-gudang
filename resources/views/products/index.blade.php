@extends('layouts.app')

@section('title', 'Katalog & Master Data Barang')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog & Stok Barang ISP</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar perangkat jaringan aktif, kabel FO drop core, aksesoris pasif, dan perkakas teknisi.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Dropdown Export Excel & PDF -->
            <div class="relative" x-data="{ exportMenu: false }">
                <button type="button" @click="exportMenu = !exportMenu" @click.outside="exportMenu = false" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-semibold text-sm shadow-2xs transition-colors cursor-pointer min-h-[44px]">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Export Laporan</span>
                    <svg class="w-3.5 h-3.5 text-emerald-700 transition-transform" :class="exportMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="exportMenu" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-30 divide-y divide-slate-100">
                    <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pilih Format File</div>
                    <div class="py-1">
                        <a href="{{ route('export.products', array_merge(request()->query(), ['type' => 'excel'])) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Excel Spreadsheet (.xlsx)</span>
                        </a>
                        <a href="{{ route('export.products', array_merge(request()->query(), ['type' => 'pdf'])) }}" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Dokumen Cetak (.pdf)</span>
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Tambah Barang Baru</span>
            </a>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Keyword -->
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / SKU / Barcode / SN..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Kategori -->
            <div>
                <select name="category_id" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Stok -->
            <div>
                <select name="status" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status Stok</option>
                    <option value="SAFE" {{ request('status') == 'SAFE' ? 'selected' : '' }}>Aman / Tersedia</option>
                    <option value="LOW" {{ request('status') == 'LOW' ? 'selected' : '' }}>Menipis (&le; Min)</option>
                    <option value="EMPTY" {{ request('status') == 'EMPTY' ? 'selected' : '' }}>Habis (0)</option>
                    <option value="DAMAGED" {{ request('status') == 'DAMAGED' ? 'selected' : '' }}>Ada Stok Rusak</option>
                </select>
            </div>

            <!-- Lokasi Rak -->
            <div class="flex items-center gap-2">
                <select name="location_id" class="flex-1 py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Lokasi Rak</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl min-h-[40px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL DATA PRODUK -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 sticky top-0">
                    <tr>
                        <th class="py-3.5 px-6">Identitas & Perangkat</th>
                        <th class="py-3.5 px-4">Kategori & Rak</th>
                        <th class="py-3.5 px-4 text-center">Status Stok</th>
                        <th class="py-3.5 px-4 text-right">Saldo Gudang</th>
                        <th class="py-3.5 px-4 text-center">Serial Number</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Identitas -->
                            <td class="py-4 px-6">
                                <a href="{{ route('products.show', $prod) }}" class="font-bold text-slate-900 hover:text-emerald-600 block">
                                    {{ $prod->name }}
                                </a>
                                <div class="flex items-center gap-2 mt-1 text-xs">
                                    <span class="font-mono bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md font-medium border border-slate-200">{{ $prod->sku }}</span>
                                    @if($prod->barcode)
                                        <span class="text-slate-400 font-mono">{{ $prod->barcode }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kategori & Lokasi -->
                            <td class="py-4 px-4 text-xs">
                                <span class="font-semibold text-slate-800 block">{{ $prod->category?->name ?? '-' }}</span>
                                <span class="text-slate-500 block mt-0.5">{{ $prod->location?->name ?? 'Gudang Utama' }}</span>
                            </td>

                            <!-- Badge Status -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $prod->status_badge }}">
                                    {{ $prod->status_label }}
                                </span>
                            </td>

                            <!-- Saldo Gudang -->
                            <td class="py-4 px-4 text-right">
                                <div class="font-bold text-sm text-slate-900">{{ $prod->formatted_stock }}</div>
                                @if($prod->has_damaged)
                                    <div class="text-[11px] text-orange-600 font-medium mt-0.5">
                                        Rusak: {{ $prod->formatted_damaged_stock }}
                                    </div>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-0.5">Min: {{ number_format($prod->min_stock) }}</div>
                            </td>

                            <!-- Serial Number Tracking -->
                            <td class="py-4 px-4 text-center">
                                @if($prod->has_serial_number)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        SN Aktif
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 font-medium">Satuan / Meter</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('products.show', $prod) }}" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Lihat Detail & Serial">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('products.edit', $prod) }}" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Ubah Master">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Tidak ada data barang yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card-Stack View (Smartphone/Tablet) -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($products as $prod)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="font-mono text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md font-semibold">{{ $prod->sku }}</span>
                            <a href="{{ route('products.show', $prod) }}" class="font-bold text-slate-900 block mt-1 hover:text-emerald-600">
                                {{ $prod->name }}
                            </a>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border shrink-0 {{ $prod->status_badge }}">
                            {{ $prod->status_label }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div>
                            <span class="text-slate-400 block text-[10px]">Stok Aktif:</span>
                            <span class="font-bold text-sm text-slate-900">{{ $prod->formatted_stock }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px]">Lokasi Rak:</span>
                            <span class="font-medium text-slate-800">{{ $prod->location?->code ?? 'Gudang' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs text-slate-500">{{ $prod->category?->name ?? '-' }}</span>
                        <a href="{{ route('products.show', $prod) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                            Detail & Serial &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">Tidak ada produk ditemukan.</div>
            @endforelse
        </div>

        <!-- PAGINATION -->
        @if($products->hasPages())
            <div class="p-4 px-6 border-t border-slate-200 bg-slate-50/50">
                {{ $products->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
