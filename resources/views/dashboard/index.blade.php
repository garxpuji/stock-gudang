@extends('layouts.app')

@section('title', 'Dashboard Logistik & Inventaris')

@section('content')
<div class="space-y-6">

    <!-- PAGE TITLE & HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Logistik & Inventaris</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Live MariaDB
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Pusat kendali perangkat aktif, kabel fiber optik, dan alat kerja teknisi Gayuh Media Informatika.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span>Keluar Barang (Loket Teknisi)</span>
            </a>
            <a href="{{ route('returns.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-sm shadow-2xs transition-colors min-h-[44px]">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                <span>Terima Retur & QC</span>
            </a>
        </div>
    </div>

    <!-- KPI STAT CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        
        <!-- Total SKU -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total SKU</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ number_format($totalProducts) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Item Terdaftar</div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs {{ $lowStockCount > 0 ? 'ring-2 ring-amber-500/20' : '' }}">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-amber-700">Stok Menipis</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-amber-600">{{ number_format($lowStockCount) }}</div>
            <div class="text-[11px] text-amber-700 font-medium mt-1">&le; Batas Minimum</div>
        </div>

        <!-- Stok Habis -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs {{ $outOfStockCount > 0 ? 'ring-2 ring-rose-500/20' : '' }}">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-rose-700">Stok Habis</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-rose-600">{{ number_format($outOfStockCount) }}</div>
            <div class="text-[11px] text-rose-600 font-medium mt-1">Perlu Restock</div>
        </div>

        <!-- Karantina / Rusak -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs {{ $damagedStockCount > 0 ? 'ring-2 ring-orange-500/20' : '' }}">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-orange-700">Karantina</span>
                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-orange-600">{{ number_format($damagedStockCount) }}</div>
            <div class="text-[11px] text-orange-700 font-medium mt-1">Unit Rusak / RMA</div>
        </div>

        <!-- Pengeluaran Bulan Ini -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Outbound</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-sky-700">{{ number_format($monthlyOutboundTeknisiCount) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">SPK Bulan Ini</div>
        </div>

        <!-- Retur Bulan Ini -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Retur & QC</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-700">{{ number_format($monthlyReturnsCount) }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Transaksi Selesai</div>
        </div>

    </div>

    <!-- DUA KOLOM: ALERT STOK KRITIS & BARANG RUSAK KARANTINA -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- ALERT STOK KRITIS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                    <h2 class="text-sm font-bold text-slate-900">Perangkat & Material Perlu Restock</h2>
                </div>
                <a href="{{ route('products.index', ['status' => 'LOW']) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($criticalStockProducts as $prod)
                    <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/80 transition-colors">
                        <div class="min-w-0 pr-4">
                            <a href="{{ route('products.show', $prod) }}" class="text-sm font-semibold text-slate-900 hover:text-emerald-600 truncate block">
                                {{ $prod->name }}
                            </a>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="font-mono text-slate-600 font-medium">{{ $prod->sku }}</span>
                                <span>&bull;</span>
                                <span>{{ $prod->location?->name ?? 'Gudang Utama' }}</span>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $prod->status_badge }}">
                                {{ $prod->formatted_stock }}
                            </span>
                            <span class="block text-[11px] text-slate-400 mt-1">Min: {{ number_format($prod->min_stock) }} {{ $prod->unit?->abbreviation }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">
                        <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Seluruh stok perangkat & kabel berada dalam kondisi aman.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- BARANG RUSAK MENUNGGU TINDAKAN (RMA / DISPOSAL) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                    <h2 class="text-sm font-bold text-slate-900">Area Karantina (Menunggu Keputusan)</h2>
                </div>
                <a href="{{ route('damaged-goods.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Kelola Karantina &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($pendingDamagedGoods as $dmg)
                    <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/80 transition-colors">
                        <div class="min-w-0 pr-4">
                            <a href="{{ route('damaged-goods.show', $dmg) }}" class="text-sm font-semibold text-slate-900 hover:text-emerald-600 truncate block">
                                {{ $dmg->product->name }}
                            </a>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                <span class="font-mono text-xs font-bold text-orange-700">{{ $dmg->code }}</span>
                                @if($dmg->serial_number)
                                    <span>&bull;</span>
                                    <span class="font-mono font-medium text-slate-700">SN: {{ $dmg->serial_number }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-1 italic">"{{ $dmg->damage_description }}"</p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold border {{ $dmg->damage_category->color() }}">
                                {{ $dmg->damage_category->value }}
                            </span>
                            <span class="block text-[11px] text-slate-400 mt-1">{{ $dmg->reported_by }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">
                        <svg class="w-8 h-8 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Tidak ada unit rusak yang tertahan di area karantina.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- DUA KOLOM: PENGELUARAN TERBARU & KARTU MUTASI TERPADU -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- PENGELUARAN TERBARU KE TEKNISI -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Pengeluaran Terakhir ke Teknisi & Divisi</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Histori serah terima perangkat di loket gudang</p>
                </div>
                <a href="{{ route('stock-out.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentDispatches as $disp)
                    <div class="p-4 px-6 hover:bg-slate-50/80 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-mono text-xs font-bold text-emerald-700">{{ $disp->transaction_code }}</span>
                            <span class="text-xs text-slate-400">{{ $disp->transaction_date->diffForHumans() }}</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-slate-900">{{ $disp->picked_up_by }}</span>
                                <span class="text-xs text-slate-500 block">{{ $disp->recipient_department_or_team }} &bull; <strong class="text-slate-700">{{ $disp->purpose }}</strong></span>
                            </div>

                            <a href="{{ route('stock-out.print', $disp) }}" target="_blank" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Cetak Surat Serah Terima">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada transaksi pengeluaran barang.</div>
                @endforelse
            </div>
        </div>

        <!-- MUTASI KARTU STOK TERPADU (LIVE AUDIT) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
            <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Kartu Stok Terpadu (Audit Mutasi)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan pergerakan fisik barang bersifat immutable</p>
                </div>
                <a href="{{ route('mutations.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Audit Ledger &rarr;</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentMutations as $mut)
                    <div class="p-4 px-6 flex items-center justify-between hover:bg-slate-50/80 transition-colors text-xs">
                        <div class="min-w-0 pr-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md font-semibold text-[10px] border {{ $mut->mutation_type->badge() }}">
                                    {{ $mut->mutation_type->label() }}
                                </span>
                                <span class="font-bold text-slate-900 truncate">{{ $mut->product->name }}</span>
                            </div>
                            <div class="text-slate-500 mt-1 flex items-center gap-2">
                                <span>Penerima/Pelapor: <strong class="text-slate-700">{{ $mut->person_in_charge }}</strong></span>
                                @if($mut->reference_ticket_no)
                                    <span>&bull;</span>
                                    <span class="font-mono text-emerald-700 font-semibold">{{ $mut->reference_ticket_no }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="font-bold text-sm {{ $mut->qty < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $mut->qty > 0 ? '+'.number_format($mut->qty, 0) : number_format($mut->qty, 0) }} {{ $mut->product->unit?->abbreviation }}
                            </span>
                            <span class="block text-[10px] text-slate-400">Saldo: {{ number_format($mut->current_stock, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada mutasi stok terdaftar.</div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- RINGKASAN KATEGORI INVENTARIS ISP -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6">
        <h2 class="text-sm font-bold text-slate-900 mb-4">Distribusi Aset Berdasarkan Kategori ISP</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categoriesBreakdown as $cat)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $cat['code'] }}</div>
                    <div class="text-sm font-bold text-slate-900 mt-1 line-clamp-1" title="{{ $cat['name'] }}">{{ $cat['name'] }}</div>
                    <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-200">
                        <span class="text-slate-600">{{ $cat['products_count'] }} SKU</span>
                        <span class="font-bold text-emerald-700">{{ number_format($cat['total_stock'], 0) }} Unit/Mtr</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
