@extends('layouts.app')

@section('title', 'Riwayat Pengeluaran Barang (Outbound)')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Barang Keluar (Outbound)</h1>
            <p class="text-sm text-slate-500 mt-1">Distribusi perangkat dan material ke Teknisi Lapangan (PSB/Maintenance) & Divisi Internal.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Form Serah Terima (Loket)</span>
            </a>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('stock-out.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Transaksi / Nama Teknisi / No. Tiket SPK..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div>
                <select name="recipient_type" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Penerima</option>
                    <option value="TEKNISI" {{ request('recipient_type') == 'TEKNISI' ? 'selected' : '' }}>Teknisi Lapangan</option>
                    <option value="KARYAWAN" {{ request('recipient_type') == 'KARYAWAN' ? 'selected' : '' }}>Karyawan / Internal</option>
                </select>
            </div>

            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-center gap-2">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="flex-1 py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl min-h-[40px]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- DATA TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        
        <!-- Desktop View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">No. Transaksi & Tanggal</th>
                        <th class="py-3.5 px-4">Pengambil Fisik (Wajib)</th>
                        <th class="py-3.5 px-4">Divisi & No. Tiket/SPK</th>
                        <th class="py-3.5 px-4">Tujuan Pemakaian</th>
                        <th class="py-3.5 px-4 text-center">Total Item</th>
                        <th class="py-3.5 px-4">Petugas Gudang</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6">
                                <a href="{{ route('stock-out.show', $tx) }}" class="font-mono font-bold text-emerald-700 hover:underline block">
                                    {{ $tx->transaction_code }}
                                </a>
                                <span class="text-xs text-slate-400 block mt-0.5">{{ $tx->transaction_date->format('d/m/Y H:i') }}</span>
                            </td>

                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block">{{ $tx->picked_up_by }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border mt-0.5 {{ $tx->recipient_type->badge() }}">
                                    {{ $tx->recipient_type->value }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-xs">
                                <span class="font-semibold text-slate-800 block">{{ $tx->recipient_department_or_team }}</span>
                                @if($tx->reference_ticket_no)
                                    <span class="font-mono text-emerald-700 font-bold block mt-0.5">{{ $tx->reference_ticket_no }}</span>
                                @else
                                    <span class="text-slate-400 block mt-0.5">-</span>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-xs max-w-xs">
                                <span class="font-medium text-slate-900 block truncate" title="{{ $tx->purpose }}">{{ $tx->purpose }}</span>
                                @if($tx->keterangan_pengeluaran)
                                    <span class="text-slate-400 block truncate mt-0.5">{{ $tx->keterangan_pengeluaran }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-center font-bold text-slate-900">
                                {{ $tx->total_items }} SKU
                            </td>

                            <td class="py-4 px-4 text-xs text-slate-600">
                                {{ $tx->dispatchedBy->name }}
                            </td>

                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('stock-out.print', $tx) }}" target="_blank" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Cetak Bukti Serah Terima">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                    <a href="{{ route('stock-out.show', $tx) }}" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">Belum ada riwayat pengeluaran barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card-Stack View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($transactions as $tx)
                <div class="p-4 space-y-2.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <a href="{{ route('stock-out.show', $tx) }}" class="font-mono text-xs font-bold text-emerald-700">
                                {{ $tx->transaction_code }}
                            </a>
                            <span class="text-xs text-slate-400 block">{{ $tx->transaction_date->format('d/m/Y H:i') }}</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $tx->recipient_type->badge() }}">
                            {{ $tx->recipient_type->value }}
                        </span>
                    </div>

                    <div>
                        <span class="text-sm font-bold text-slate-900 block">{{ $tx->picked_up_by }}</span>
                        <span class="text-xs text-slate-500 block">{{ $tx->recipient_department_or_team }} &bull; Ref: {{ $tx->reference_ticket_no ?? '-' }}</span>
                        <p class="text-xs text-slate-700 mt-1 font-medium">{{ $tx->purpose }}</p>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-600">{{ $tx->total_items }} SKU dikeluarkan</span>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('stock-out.print', $tx) }}" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-emerald-600 px-2.5 py-1 rounded bg-slate-100">
                                Cetak
                            </a>
                            <a href="{{ route('stock-out.show', $tx) }}" class="text-xs font-semibold text-emerald-600 px-2.5 py-1 rounded bg-emerald-50">
                                Detail &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-slate-500">Belum ada transaksi pengeluaran barang.</div>
            @endforelse
        </div>

        @if($transactions->hasPages())
            <div class="p-4 px-6 border-t border-slate-200 bg-slate-50/50">
                {{ $transactions->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
