@extends('layouts.app')

@section('title', 'Penerimaan Barang Kembali (Retur & QC)')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Barang Kembali (Retur & QC)</h1>
            <p class="text-sm text-slate-500 mt-1">Penerimaan sisa material proyek, pengembalian alat ukur, dan modem dismantle pelanggan dengan inspeksi QC.</p>
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
                        <a href="{{ route('export.returns', array_merge(request()->query(), ['type' => 'excel'])) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Excel Spreadsheet (.xlsx)</span>
                        </a>
                        <a href="{{ route('export.returns', array_merge(request()->query(), ['type' => 'pdf'])) }}" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-800 transition-colors">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Dokumen Cetak (.pdf)</span>
                        </a>
                    </div>
                </div>
            </div>

            <a href="{{ route('returns.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Retur & QC Baru</span>
            </a>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('returns.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Retur / No. Tiket / Kategori..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Filter Teknisi Pengembali -->
            <div>
                <select name="technician" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Teknisi / Pengembali</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech }}" {{ request('technician') == $tech ? 'selected' : '' }}>{{ $tech }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Hasil QC -->
            <div>
                <select name="qc_status" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Hasil QC</option>
                    @foreach($qcStatuses as $qc)
                        <option value="{{ $qc->value }}" {{ request('qc_status') == $qc->value ? 'selected' : '' }}>{{ $qc->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" title="Tanggal Dari" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" title="Tanggal Sampai" class="flex-1 py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl min-h-[40px] cursor-pointer">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'technician', 'qc_status', 'date_from', 'date_to']))
                    <a href="{{ route('returns.index') }}" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DATA TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">No. Retur & Waktu</th>
                        <th class="py-3.5 px-4">Pengembali (Wajib)</th>
                        <th class="py-3.5 px-4">Kategori Pengembalian</th>
                        <th class="py-3.5 px-4">Ref. Tiket / SPK</th>
                        <th class="py-3.5 px-4 text-center">Hasil Inspeksi QC</th>
                        <th class="py-3.5 px-4">Petugas Gudang</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($returns as $ret)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6">
                                <a href="{{ route('returns.show', $ret) }}" class="font-mono font-bold text-emerald-700 hover:underline block">
                                    {{ $ret->transaction_code }}
                                </a>
                                <span class="text-xs text-slate-400 block mt-0.5">{{ $ret->transaction_date->format('d/m/Y H:i') }}</span>
                            </td>

                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block">{{ $ret->returned_by }}</span>
                                <span class="text-xs text-slate-500 block">{{ $ret->returned_by_contact ?? $ret->recipient_type->value }}</span>
                            </td>

                            <td class="py-4 px-4 text-xs font-semibold text-slate-800">
                                {{ $ret->return_category }}
                            </td>

                            <td class="py-4 px-4 text-xs font-mono font-bold text-emerald-700">
                                {{ $ret->reference_ticket_no ?? '-' }}
                            </td>

                            <td class="py-4 px-4 text-center">
                                @php
                                    $hasDamaged = $ret->items->contains(fn($i) => $i->qc_status === \App\Enums\QcStatus::DAMAGED);
                                    $hasGood = $ret->items->contains(fn($i) => $i->qc_status === \App\Enums\QcStatus::GOOD);
                                @endphp
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($hasGood)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Restock Baik
                                        </span>
                                    @endif
                                    @if($hasDamaged)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
                                            Karantina Rusak
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 px-4 text-xs text-slate-600">
                                {{ $ret->receivedBy->name }}
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('returns.print', $ret) }}" target="_blank" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Cetak Bukti Penerimaan Retur">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <a href="{{ route('returns.show', $ret) }}" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">Belum ada riwayat pengembalian barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div class="p-4 px-6 border-t border-slate-200 bg-slate-50/50">
                {{ $returns->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
