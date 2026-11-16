@extends('layouts.app')

@section('title', 'Area Karantina & Barang Rusak')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Area Karantina & Barang Rusak</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Pengelolaan perangkat rusak hasil QC retur & laporan lapangan: Klaim Garansi Vendor (RMA), Write-Off, atau Servis Internal.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Dropdown Export Excel & PDF -->
            <div class="relative" x-data="{ exportMenu: false }">
                <button type="button" @click="exportMenu = !exportMenu" @click.outside="exportMenu = false" class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-xl bg-orange-50 hover:bg-orange-100 text-orange-800 border border-orange-300 font-semibold text-sm shadow-2xs transition-colors cursor-pointer min-h-[44px]">
                    <svg class="w-4 h-4 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Export Laporan</span>
                    <svg class="w-3.5 h-3.5 text-orange-700 transition-transform" :class="exportMenu ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="exportMenu" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 py-1.5 z-30 divide-y divide-slate-100">
                    <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Pilih Format File</div>
                    <div class="py-1">
                        <a href="{{ route('export.damaged-goods', array_merge(request()->query(), ['type' => 'excel'])) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-orange-50 hover:text-orange-800 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                            <span>Excel Spreadsheet (.xlsx)</span>
                        </a>
                        <a href="{{ route('export.damaged-goods', array_merge(request()->query(), ['type' => 'pdf'])) }}" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Dokumen Cetak (.pdf)</span>
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('damaged-goods.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Lapor Kerusakan Langsung</span>
            </a>
        </div>
    </div>

    <!-- KPI STATS KARANTINA -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('damaged-goods.index', ['action_taken' => 'PENDING']) }}" class="p-4 rounded-2xl border transition-all {{ request('action_taken') === 'PENDING' ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-500/20' : 'bg-white border-slate-200 hover:bg-slate-50' }}">
            <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider block">Menunggu Tindakan</span>
            <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $pendingCount }}</span>
            <span class="text-[11px] text-slate-500 block mt-0.5">Tertahan di Karantina</span>
        </a>

        <a href="{{ route('damaged-goods.index', ['action_taken' => 'RMA_VENDOR']) }}" class="p-4 rounded-2xl border transition-all {{ request('action_taken') === 'RMA_VENDOR' ? 'bg-purple-50/70 border-purple-300 ring-2 ring-purple-500/20' : 'bg-white border-slate-200 hover:bg-slate-50' }}">
            <span class="text-xs font-semibold text-purple-800 uppercase tracking-wider block">Klaim Garansi (RMA)</span>
            <span class="text-2xl font-bold text-purple-600 mt-1 block">{{ $rmaCount }}</span>
            <span class="text-[11px] text-slate-500 block mt-0.5">Proses Vendor/Distributor</span>
        </a>

        <a href="{{ route('damaged-goods.index', ['action_taken' => 'SCRAPPED']) }}" class="p-4 rounded-2xl border transition-all {{ request('action_taken') === 'SCRAPPED' ? 'bg-rose-50/70 border-rose-300 ring-2 ring-rose-500/20' : 'bg-white border-slate-200 hover:bg-slate-50' }}">
            <span class="text-xs font-semibold text-rose-800 uppercase tracking-wider block">Pemusnahan (Scrapped)</span>
            <span class="text-2xl font-bold text-rose-600 mt-1 block">{{ $scrappedCount }}</span>
            <span class="text-[11px] text-slate-500 block mt-0.5">Afkir / Write-Off Aset</span>
        </a>

        <a href="{{ route('damaged-goods.index', ['action_taken' => 'REPAIRED_INTERNAL']) }}" class="p-4 rounded-2xl border transition-all {{ request('action_taken') === 'REPAIRED_INTERNAL' ? 'bg-emerald-50/70 border-emerald-300 ring-2 ring-emerald-500/20' : 'bg-white border-slate-200 hover:bg-slate-50' }}">
            <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider block">Perbaikan Internal</span>
            <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $repairedCount }}</span>
            <span class="text-[11px] text-slate-500 block mt-0.5">Selesai & Restock</span>
        </a>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('damaged-goods.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Rusak / SN / Nama Perangkat / Pelapor..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div>
                <select name="category" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Kategori Kerusakan</option>
                    <option value="Tersambar Petir / Overvoltage" {{ request('category') === 'Tersambar Petir / Overvoltage' ? 'selected' : '' }}>Tersambar Petir / Overvoltage</option>
                    <option value="Port LAN/Fiber Rusak" {{ request('category') === 'Port LAN/Fiber Rusak' ? 'selected' : '' }}>Port LAN/Fiber Rusak</option>
                    <option value="Optic Laser Drop" {{ request('category') === 'Optic Laser Drop' ? 'selected' : '' }}>Optic Laser Drop</option>
                    <option value="Fisik Pecah / Cacat" {{ request('category') === 'Fisik Pecah / Cacat' ? 'selected' : '' }}>Fisik Pecah / Cacat</option>
                    <option value="Mati Total" {{ request('category') === 'Mati Total' ? 'selected' : '' }}>Mati Total</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="action_taken" class="flex-1 py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status Tindakan</option>
                    <option value="PENDING" {{ request('action_taken') === 'PENDING' ? 'selected' : '' }}>Pending (Karantina)</option>
                    <option value="RMA_VENDOR" {{ request('action_taken') === 'RMA_VENDOR' ? 'selected' : '' }}>RMA Vendor</option>
                    <option value="SCRAPPED" {{ request('action_taken') === 'SCRAPPED' ? 'selected' : '' }}>Scrapped / Afkir</option>
                    <option value="REPAIRED_INTERNAL" {{ request('action_taken') === 'REPAIRED_INTERNAL' ? 'selected' : '' }}>Perbaikan Internal</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl min-h-[40px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- TABEL BARANG RUSAK -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Kode & Tanggal</th>
                        <th class="py-3.5 px-4">Perangkat & Serial Number</th>
                        <th class="py-3.5 px-4">Kategori Kerusakan</th>
                        <th class="py-3.5 px-4">Pelapor / Sumber</th>
                        <th class="py-3.5 px-4 text-center">Tindakan Lanjutan</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($damagedGoods as $dmg)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6">
                            <a href="{{ route('damaged-goods.show', $dmg) }}" class="font-mono font-bold text-orange-700 hover:underline block">
                                {{ $dmg->code }}
                            </a>
                            <span class="text-xs text-slate-400 block mt-0.5">{{ $dmg->reported_at->format('d/m/Y H:i') }}</span>
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-900 block">{{ $dmg->product->name }}</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="font-mono text-xs text-slate-500">{{ $dmg->product->sku }}</span>
                                @if($dmg->serial_number)
                                <span>&bull;</span>
                                <span class="font-mono text-xs font-bold text-slate-800">SN: {{ $dmg->serial_number }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $dmg->damage_category->color() }}">
                                {{ $dmg->damage_category->value }}
                            </span>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-1 italic" title="{{ $dmg->damage_description }}">"{{ $dmg->damage_description }}"</p>
                        </td>

                        <td class="py-4 px-4 text-xs">
                            <span class="font-bold text-slate-800 block">{{ $dmg->reported_by }}</span>
                            <span class="text-slate-400 block mt-0.5">Sumber: {{ $dmg->source }}</span>
                        </td>

                        <td class="py-4 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $dmg->action_taken->badge() }}">
                                {{ $dmg->action_taken->label() }}
                            </span>
                        </td>

                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('damaged-goods.show', $dmg) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                                <span>Tindakan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-600">
                            Tidak ada barang rusak pada filter ini. Gunakan tombol <strong>Lapor Kerusakan Langsung</strong> jika menemukan unit cacat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card-Stack View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($damagedGoods as $dmg)
            <div class="p-4 space-y-2">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <a href="{{ route('damaged-goods.show', $dmg) }}" class="font-mono text-xs font-bold text-orange-700 hover:underline">
                            {{ $dmg->code }}
                        </a>
                        <span class="text-xs text-slate-500 block">{{ $dmg->reported_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $dmg->action_taken->badge() }}">
                        {{ $dmg->action_taken->label() }}
                    </span>
                </div>

                <div>
                    <span class="text-sm font-bold text-slate-900 block">{{ $dmg->product->name }}</span>
                    <span class="text-xs text-slate-600 font-mono block">{{ $dmg->product->sku }} {{ $dmg->serial_number ? '• SN: '.$dmg->serial_number : '' }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border mt-1 {{ $dmg->damage_category->color() }}">
                        {{ $dmg->damage_category->value }}
                    </span>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <span class="text-xs text-slate-600">Pelapor: {{ $dmg->reported_by }}</span>
                    <a href="{{ route('damaged-goods.show', $dmg) }}" class="inline-flex items-center px-3 py-1.5 min-h-[40px] rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                        Tindak Lanjut
                    </a>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-sm text-slate-600">
                Tidak ada barang rusak pada filter ini.
            </div>
            @endforelse
        </div>

        @if($damagedGoods->hasPages())
        <div class="p-4 px-6 border-t border-slate-200 bg-slate-50/50">
            {{ $damagedGoods->links() }}
        </div>
        @endif

    </div>

</div>
@endsection