@extends('layouts.app')

@section('title', 'Dashboard Logistik')

@section('content')
<div class="space-y-6">

    <!-- PAGE TITLE & CTA OPERASIONAL UTAMA -->
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Logistik</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pusat kendali perangkat jaringan, kabel drop core, dan alur operasional teknisi.</p>
        </div>

        <!-- CTA OPERASIONAL (SEJAJAR 1 BARIS DI DESKTOP, GRID 2 KOLOM DI MOBILE) -->
        <div class="grid grid-cols-2 sm:flex sm:flex-wrap xl:flex-nowrap items-center gap-2 w-full xl:w-auto">
            <!-- 1. Primary CTA: Keluar Barang ke Teknisi -->
            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-all hover:shadow cursor-pointer whitespace-nowrap min-h-[38px] w-full sm:w-auto">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Keluar Barang</span>
            </a>

            <!-- 2. Secondary CTA: Barang Masuk dari Vendor/Pengadaan -->
            <a href="{{ route('stock-in.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold text-sm shadow-2xs transition-all cursor-pointer whitespace-nowrap min-h-[38px] w-full sm:w-auto">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                <span>Barang Masuk</span>
            </a>

            <!-- 3. Tertiary CTA: Terima Retur dari Lapangan -->
            <a href="{{ route('returns.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold text-sm shadow-2xs transition-all cursor-pointer whitespace-nowrap min-h-[38px] w-full sm:w-auto">
                <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M13 8l4-4m0 0l4 4m-4-4v12"></path></svg>
                <span>Terima Retur & QC</span>
            </a>

            <!-- 4. Export Laporan (Excel & PDF) Dropdown -->
            <div class="relative w-full sm:w-auto" x-data="{ exportOpen: false }">
                <button type="button" @click="exportOpen = !exportOpen" @click.outside="exportOpen = false" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-semibold text-sm shadow-2xs transition-all cursor-pointer whitespace-nowrap min-h-[38px] w-full sm:w-auto" title="Export Laporan Excel (.xlsx) & PDF (.pdf)">
                    <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Export Laporan</span>
                    <svg class="w-3.5 h-3.5 text-emerald-700 transition-transform shrink-0" :class="exportOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="exportOpen" x-cloak x-transition class="absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-xl border border-slate-200 py-2 z-30 divide-y divide-slate-100">
                    <div class="px-4 py-2 flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pilih Kategori & Format</span>
                        <span class="text-[10px] text-slate-400 font-medium">Excel (.xlsx) / PDF</span>
                    </div>
                    <div class="p-2 space-y-1.5">
                        <!-- 1. Stok Tersedia -->
                        <div class="p-2.5 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span class="text-xs font-bold text-slate-900">Stok Tersedia</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('export.products', ['type' => 'excel']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors" title="Download Excel (.xlsx)">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 001 1h12a1 1 0 001-1V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H4a1 1 0 00-1 1v14zm8-12V3.414L14.586 7H12a1 1 0 01-1-1zm-4.707 6.293a1 1 0 011.414 0L10 12.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        Excel
                                    </a>
                                    <a href="{{ route('export.products', ['type' => 'pdf']) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors" title="Download PDF (.pdf)">
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 pl-4">Katalog perangkat, kabel drop core & saldo fisik rak</p>
                        </div>

                        <!-- 2. Barang Keluar -->
                        <div class="p-2.5 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                                    <span class="text-xs font-bold text-slate-900">Barang Keluar</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('export.stock-out', ['type' => 'excel']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 transition-colors" title="Download Excel (.xlsx)">
                                        <svg class="w-3 h-3 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 001 1h12a1 1 0 001-1V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H4a1 1 0 00-1 1v14zm8-12V3.414L14.586 7H12a1 1 0 01-1-1zm-4.707 6.293a1 1 0 011.414 0L10 12.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        Excel
                                    </a>
                                    <a href="{{ route('export.stock-out', ['type' => 'pdf']) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors" title="Download PDF (.pdf)">
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 pl-4">Pengambilan teknisi lapangan, tiket SPK & serah terima</p>
                        </div>

                        <!-- 3. Barang Kembali -->
                        <div class="p-2.5 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-teal-500 shrink-0"></span>
                                    <span class="text-xs font-bold text-slate-900">Barang Kembali</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('export.returns', ['type' => 'excel']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-teal-50 text-teal-700 hover:bg-teal-100 border border-teal-200 transition-colors" title="Download Excel (.xlsx)">
                                        <svg class="w-3 h-3 text-teal-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 001 1h12a1 1 0 001-1V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H4a1 1 0 00-1 1v14zm8-12V3.414L14.586 7H12a1 1 0 01-1-1zm-4.707 6.293a1 1 0 011.414 0L10 12.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        Excel
                                    </a>
                                    <a href="{{ route('export.returns', ['type' => 'pdf']) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors" title="Download PDF (.pdf)">
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 pl-4">Retur sisa material, alat kerja & hasil inspeksi QC</p>
                        </div>

                        <!-- 4. Barang Rusak -->
                        <div class="p-2.5 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-200">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                                    <span class="text-xs font-bold text-slate-900">Barang Rusak</span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <a href="{{ route('export.damaged-goods', ['type' => 'excel']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 transition-colors" title="Download Excel (.xlsx)">
                                        <svg class="w-3 h-3 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 001 1h12a1 1 0 001-1V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H4a1 1 0 00-1 1v14zm8-12V3.414L14.586 7H12a1 1 0 01-1-1zm-4.707 6.293a1 1 0 011.414 0L10 12.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                        Excel
                                    </a>
                                    <a href="{{ route('export.damaged-goods', ['type' => 'pdf']) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors" title="Download PDF (.pdf)">
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                                        PDF
                                    </a>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-500 pl-4">Area karantina, unit klaim RMA & write-off aset</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. KPI STAT CARDS (RESPONSIF BERTAHAP DENGAN BASELINE SEJAJAR) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-3.5">
        
        <!-- Total SKU -->
        <a href="{{ route('products.index') }}" class="group bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 hover:border-emerald-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 cursor-pointer flex flex-col justify-between h-full">
            <div>
                <div class="min-h-[2.25rem] flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 group-hover:text-emerald-700 transition-colors leading-tight">Total SKU</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">{{ number_format($totalProducts) }}</div>
            </div>
            <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between pt-1.5 border-t border-slate-100">
                <span>Item Terdaftar</span>
                <span class="text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity font-bold">&rarr;</span>
            </div>
        </a>

        <!-- Stok Menipis -->
        <a href="{{ route('products.index', ['status' => 'LOW']) }}" class="group bg-white p-3.5 sm:p-4 rounded-2xl border {{ $lowStockCount > 0 ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200' }} hover:border-amber-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 cursor-pointer flex flex-col justify-between h-full">
            <div>
                <div class="min-h-[2.25rem] flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-700 leading-tight">Stok Menipis</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 group-hover:bg-amber-100 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-amber-600">{{ number_format($lowStockCount) }}</div>
            </div>
            <div class="text-[11px] text-amber-700 font-medium mt-2 flex items-center justify-between pt-1.5 border-t border-slate-100">
                <span>&le; Batas Minimum</span>
                <span class="text-amber-700 opacity-0 group-hover:opacity-100 transition-opacity font-bold">&rarr;</span>
            </div>
        </a>

        <!-- Stok Habis -->
        <a href="{{ route('products.index', ['status' => 'EMPTY']) }}" class="group bg-white p-3.5 sm:p-4 rounded-2xl border {{ $outOfStockCount > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200' }} hover:border-rose-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 cursor-pointer flex flex-col justify-between h-full">
            <div>
                <div class="min-h-[2.25rem] flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-rose-700 leading-tight">Stok Habis</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 group-hover:bg-rose-100 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-rose-600">{{ number_format($outOfStockCount) }}</div>
            </div>
            <div class="text-[11px] text-rose-600 font-medium mt-2 flex items-center justify-between pt-1.5 border-t border-slate-100">
                <span>Perlu Restock</span>
                <span class="text-rose-700 opacity-0 group-hover:opacity-100 transition-opacity font-bold">&rarr;</span>
            </div>
        </a>

        <!-- Karantina / Rusak -->
        <a href="{{ route('damaged-goods.index') }}" class="group bg-white p-3.5 sm:p-4 rounded-2xl border {{ $damagedStockCount > 0 ? 'border-orange-300 bg-orange-50/20' : 'border-slate-200' }} hover:border-orange-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 cursor-pointer flex flex-col justify-between h-full">
            <div>
                <div class="min-h-[2.25rem] flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-orange-700 leading-tight">Karantina</span>
                    <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 group-hover:bg-orange-100 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-orange-600">{{ number_format($damagedStockCount) }}</div>
            </div>
            <div class="text-[11px] text-orange-700 font-medium mt-2 flex items-center justify-between pt-1.5 border-t border-slate-100">
                <span>Unit Rusak / RMA</span>
                <span class="text-orange-700 opacity-0 group-hover:opacity-100 transition-opacity font-bold">&rarr;</span>
            </div>
        </a>

        <!-- Pengeluaran Bulan Ini (Outbound) -->
        <a href="{{ route('stock-out.index') }}" class="group bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 hover:border-sky-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 cursor-pointer flex flex-col justify-between h-full">
            <div>
                <div class="min-h-[2.25rem] flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 group-hover:text-sky-700 transition-colors leading-tight">Outbound</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 group-hover:bg-sky-100 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-sky-700">{{ number_format($monthlyOutboundTeknisiCount) }}</div>
            </div>
            <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between pt-1.5 border-t border-slate-100">
                <span>SPK Bulan Ini</span>
                <span class="text-sky-600 opacity-0 group-hover:opacity-100 transition-opacity font-bold">&rarr;</span>
            </div>
        </a>

        <!-- Retur Bulan Ini -->
        <a href="{{ route('returns.index') }}" class="group bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200 hover:border-emerald-400 hover:shadow-md hover:-translate-y-0.5 transition-all duration-150 cursor-pointer flex flex-col justify-between h-full">
            <div>
                <div class="min-h-[2.25rem] flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 group-hover:text-emerald-700 transition-colors leading-tight">Retur & QC</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-emerald-700">{{ number_format($monthlyReturnsCount) }}</div>
            </div>
            <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between pt-1.5 border-t border-slate-100">
                <span>Transaksi Selesai</span>
                <span class="text-emerald-600 opacity-0 group-hover:opacity-100 transition-opacity font-bold">&rarr;</span>
            </div>
        </a>

    </div>

    <!-- 3. DUA KOLOM: ALERT STOK KRITIS & BARANG RUSAK KARANTINA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- ALERT STOK KRITIS (PERLU RESTOCK) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col">
            <div class="p-4 px-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                    <h2 class="text-sm font-bold text-slate-900">Perlu Restock Segera</h2>
                </div>
                <a href="{{ route('products.index', ['status' => 'LOW']) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group">
                    <span>Lihat Semua</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($criticalStockProducts as $prod)
                    <a href="{{ route('products.show', $prod) }}" class="p-3.5 px-5 flex items-center justify-between hover:bg-slate-50 transition-colors group cursor-pointer block">
                        <div class="min-w-0 flex-1 pr-3">
                            <span class="text-sm font-semibold text-slate-900 group-hover:text-emerald-600 truncate block transition-colors">
                                {{ $prod->name }}
                            </span>
                            <div class="flex items-center gap-2 min-w-0 text-xs text-slate-500 mt-1">
                                <span class="font-mono text-slate-700 font-semibold bg-slate-100 px-1.5 py-0.5 rounded text-[11px] shrink-0 whitespace-nowrap">{{ $prod->sku }}</span>
                                <span class="text-slate-300 shrink-0">&bull;</span>
                                <span class="truncate text-slate-600">{{ $prod->location?->name ?? 'Gudang Utama' }}</span>
                            </div>
                        </div>

                        <div class="text-right shrink-0 flex items-center gap-2.5 sm:gap-3">
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prod->status_badge }}">
                                    {{ $prod->formatted_stock }}
                                </span>
                                <span class="block text-[10px] text-slate-400 mt-0.5 whitespace-nowrap">Min: {{ number_format($prod->min_stock) }} {{ $prod->unit?->abbreviation }}</span>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500 flex flex-col items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Seluruh stok perangkat & material berada dalam kondisi aman.</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- BARANG RUSAK MENUNGGU TINDAKAN (AREA KARANTINA) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col">
            <div class="p-4 px-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    <h2 class="text-sm font-bold text-slate-900">Area Karantina (Menunggu Keputusan)</h2>
                </div>
                <a href="{{ route('damaged-goods.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group">
                    <span>Kelola Karantina</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($pendingDamagedGoods as $dmg)
                    <a href="{{ route('damaged-goods.show', $dmg) }}" class="p-3.5 px-5 hover:bg-slate-50 transition-colors group cursor-pointer block">
                        <!-- Baris Atas: Nama Perangkat (Kiri) + Badge Status Kerusakan (Kanan / Turun di mobile jika sempit) -->
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1 sm:gap-3">
                            <span class="text-sm font-semibold text-slate-900 group-hover:text-emerald-600 truncate transition-colors flex-1 min-w-0" title="{{ $dmg->product->name }}">
                                {{ $dmg->product->name }}
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border self-start sm:self-auto shrink-0 whitespace-nowrap {{ $dmg->damage_category->color() }}">
                                {{ $dmg->damage_category->value }}
                            </span>
                        </div>

                        <!-- Baris Tengah: Metadata Teknis (Kode Kerusakan + Serial Number) -->
                        <div class="flex items-center flex-wrap gap-x-2 gap-y-0.5 mt-1.5 text-xs">
                            <span class="font-mono text-xs font-bold text-orange-700 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-200 whitespace-nowrap">{{ $dmg->code }}</span>
                            @if($dmg->serial_number)
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-mono font-medium text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded text-[11px] whitespace-nowrap">SN: {{ $dmg->serial_number }}</span>
                            @endif
                        </div>

                        <!-- Baris Bawah: Catatan Kerusakan (Kiri) + Nama Pelapor/Teknisi (Kanan) -->
                        <div class="flex items-center justify-between gap-3 mt-2 text-xs">
                            <p class="text-slate-500 italic line-clamp-1 flex-1">"{{ $dmg->damage_description }}"</p>
                            <span class="text-slate-600 font-medium shrink-0 flex items-center gap-1 text-[11px]">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="whitespace-nowrap">{{ $dmg->reported_by }}</span>
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500 flex flex-col items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Tidak ada unit rusak yang tertahan di area karantina.</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 4. DUA KOLOM: PENGELUARAN TERBARU & KARTU MUTASI TERPADU -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        <!-- PENGELUARAN TERAKHIR KE TEKNISI -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col h-full">
            <div class="p-4 px-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Pengeluaran Terakhir ke Teknisi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Histori serah terima perangkat di loket gudang</p>
                </div>
                <a href="{{ route('stock-out.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group">
                    <span>Lihat Semua</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($recentDispatches as $disp)
                    <div class="p-3.5 px-5 hover:bg-slate-50 transition-colors flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1 pr-3">
                            <div class="flex items-center gap-2 mb-0.5">
                                <a href="{{ route('stock-out.show', $disp) }}" class="font-mono text-xs font-bold text-emerald-700 hover:underline shrink-0">
                                    {{ $disp->transaction_code }}
                                </a>
                                <span class="text-slate-300 shrink-0">&bull;</span>
                                <span class="text-[11px] text-slate-400 shrink-0">{{ $disp->transaction_date->diffForHumans() }}</span>
                            </div>
                            <span class="text-sm font-bold text-slate-900 truncate block">{{ $disp->picked_up_by }}</span>
                            <span class="text-xs text-slate-500 block truncate mt-0.5">{{ $disp->recipient_department_or_team }} &bull; <strong class="text-slate-700 font-medium">{{ $disp->purpose }}</strong></span>
                        </div>

                        <a href="{{ route('stock-out.print', $disp) }}" target="_blank" class="self-center p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors cursor-pointer shrink-0" title="Cetak Surat Serah Terima">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada transaksi pengeluaran barang.</div>
                @endforelse
            </div>
        </div>

        <!-- MUTASI KARTU STOK TERPADU (LIVE AUDIT) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden flex flex-col h-full">
            <div class="p-4 px-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Kartu Stok Terpadu (Audit Mutasi)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan pergerakan fisik barang terbaru</p>
                </div>
                <a href="{{ route('mutations.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group">
                    <span>Audit Ledger</span>
                    <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                </a>
            </div>

            <div class="divide-y divide-slate-100 flex-1">
                @forelse($recentMutations as $mut)
                    <div class="p-3.5 px-5 hover:bg-slate-50 transition-colors">
                        <!-- Baris 1: Nama Lengkap Perangkat & Mutasi / Saldo -->
                        <div class="flex items-start justify-between gap-3">
                            <span class="font-semibold text-slate-800 truncate flex-1 min-w-0 text-sm" title="{{ $mut->product->name }}">
                                {{ $mut->product->name }}
                            </span>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-sm {{ $mut->qty < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $mut->qty > 0 ? '+'.number_format($mut->qty, 0) : number_format($mut->qty, 0) }} {{ $mut->product->unit?->abbreviation }}
                                </span>
                                <span class="block text-[10px] text-slate-400">Saldo: {{ number_format($mut->current_stock, 0) }}</span>
                            </div>
                        </div>

                        <!-- Baris 2: Badge Mutasi, Penerima & No Referensi -->
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs mt-1.5 text-slate-500">
                            <span class="px-2 py-0.5 rounded-md font-semibold text-[10px] border shrink-0 {{ $mut->mutation_type->badge() }}">
                                {{ $mut->mutation_type->label() }}
                            </span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="truncate">Penerima: <strong class="text-slate-700 font-medium">{{ $mut->person_in_charge }}</strong></span>
                            @if($mut->reference_ticket_no)
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-mono text-emerald-700 font-semibold shrink-0">{{ $mut->reference_ticket_no }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada mutasi stok terdaftar.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- 5. RINGKASAN KATEGORI INVENTARIS ISP (CLICKABLE) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-5">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Distribusi Aset Kategori ISP</h2>
                <p class="text-xs text-slate-500 mt-0.5">Klik kategori untuk memfilter stok barang</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 group">
                <span>Semua Kategori</span>
                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3.5">
            @foreach($categoriesBreakdown as $cat)
                <a href="{{ route('products.index', ['category_id' => $cat['id'] ?? '']) }}" class="p-3.5 rounded-xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-200 hover:border-emerald-300 transition-all cursor-pointer flex flex-col justify-between h-full group">
                    <div>
                        <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider group-hover:text-emerald-700 transition-colors">{{ $cat['code'] }}</div>
                        <div class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 mt-1 line-clamp-2 min-h-[2.5rem] leading-snug transition-colors" title="{{ $cat['name'] }}">{{ $cat['name'] }}</div>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between text-[11px] pt-2 border-t border-slate-200/80">
                        <span class="text-slate-600">{{ $cat['products_count'] }} SKU</span>
                        <span class="font-bold text-emerald-700">{{ number_format($cat['total_stock'], 0) }} Unit/Mtr</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

</div>
@endsection
