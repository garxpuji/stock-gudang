@extends('layouts.app')

@section('title', 'Barang Masuk (Inbound / Pengadaan)')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Barang Masuk (Inbound)</h1>
            <p class="text-sm text-slate-500 mt-1">Penerimaan pasokan perangkat & material dari vendor supplier / distributor resmi.</p>
        </div>

        <a href="{{ route('stock-in.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Penerimaan Masuk Baru</span>
        </a>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('stock-in.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="lg:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. PO / No. Surat Jalan / Supplier / Kurir..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-emerald-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
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

    <!-- TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">No. Transaksi & Tanggal</th>
                        <th class="py-3.5 px-4">Supplier / Vendor</th>
                        <th class="py-3.5 px-4">No. PO & Surat Jalan</th>
                        <th class="py-3.5 px-4">Kurir / Pengantar</th>
                        <th class="py-3.5 px-4 text-center">Item</th>
                        <th class="py-3.5 px-4 text-right">Total Nilai</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6">
                                <a href="{{ route('stock-in.show', $tx) }}" class="font-mono font-bold text-emerald-700 hover:underline block">
                                    {{ $tx->transaction_code }}
                                </a>
                                <span class="text-xs text-slate-400 block mt-0.5">{{ $tx->transaction_date->format('d/m/Y') }}</span>
                            </td>

                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block">{{ $tx->supplier?->name ?? $tx->supplier_name }}</span>
                                <span class="text-xs text-slate-500">Penerima: {{ $tx->receivedBy->name }}</span>
                            </td>

                            <td class="py-4 px-4 text-xs">
                                @if($tx->po_number)
                                    <span class="font-mono font-semibold text-slate-800 block">PO: {{ $tx->po_number }}</span>
                                @endif
                                @if($tx->vendor_delivery_order_no)
                                    <span class="font-mono text-slate-500 block">SJ: {{ $tx->vendor_delivery_order_no }}</span>
                                @endif
                            </td>

                            <td class="py-4 px-4 text-xs font-medium text-slate-800">
                                {{ $tx->courier_or_driver_name ?? '-' }}
                            </td>

                            <td class="py-4 px-4 text-center font-bold text-slate-900">
                                {{ $tx->total_items }} SKU
                            </td>

                            <td class="py-4 px-4 text-right font-bold text-emerald-700">
                                Rp {{ number_format($tx->total_cost, 0, ',', '.') }}
                            </td>

                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('stock-in.show', $tx) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
                                    <span>Detail</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-600">
                                Belum ada data barang masuk. Klik tombol <strong>Penerimaan Masuk Baru</strong> untuk mencatat pengadaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card-Stack View -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($transactions as $tx)
                <div class="p-4 space-y-2">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <a href="{{ route('stock-in.show', $tx) }}" class="font-mono text-xs font-bold text-emerald-700 hover:underline">
                                {{ $tx->transaction_code }}
                            </a>
                            <span class="text-xs text-slate-500 block">{{ $tx->transaction_date->format('d/m/Y') }}</span>
                        </div>
                        <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            {{ $tx->total_items }} SKU
                        </span>
                    </div>

                    <div>
                        <span class="text-sm font-bold text-slate-900 block">{{ $tx->supplier?->name ?? ($tx->supplier_name ?? 'Vendor') }}</span>
                        @if($tx->po_number || $tx->vendor_delivery_order_no)
                            <span class="text-xs text-slate-600 font-mono block mt-0.5">
                                {{ $tx->po_number ? 'PO: '.$tx->po_number : '' }} {{ $tx->vendor_delivery_order_no ? 'SJ: '.$tx->vendor_delivery_order_no : '' }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                        <span class="text-xs font-bold text-slate-800">Rp {{ number_format($tx->total_cost, 0, ',', '.') }}</span>
                        <a href="{{ route('stock-in.show', $tx) }}" class="inline-flex items-center px-3 py-1.5 min-h-[40px] rounded-lg text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-sm text-slate-600">
                    Belum ada data barang masuk. Klik tombol <strong>Penerimaan Masuk Baru</strong> di atas untuk memulai.
                </div>
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
