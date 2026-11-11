@extends('layouts.app')

@section('title', 'Penerimaan Inbound - ' . $stockIn->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('stock-in.index') }}" class="hover:text-emerald-600">Riwayat Inbound</a>
                <span>/</span>
                <span class="font-mono text-emerald-700 font-bold">{{ $stockIn->transaction_code }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Barang Masuk (Inbound)</h1>
        </div>

        <a href="{{ route('stock-in.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali
        </a>
    </div>

    <!-- DOCUMENT CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">Gayuh Media Informatika</h2>
                    <span class="text-xs text-slate-500 block">Warehouse Inbound & Procurement Reception</span>
                    <span class="text-xs font-semibold text-emerald-700 block mt-0.5">BUKTI PENERIMAAN BARANG MASUK (INBOUND)</span>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="font-mono text-base font-bold text-emerald-700 block">{{ $stockIn->transaction_code }}</span>
                <span class="text-xs text-slate-500 block">Tanggal: {{ $stockIn->transaction_date->format('d F Y') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-sm">
            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Supplier / Vendor:</span>
                <span class="font-bold text-slate-900 text-base block mt-0.5">{{ $stockIn->supplier?->name ?? $stockIn->supplier_name }}</span>
                <span class="text-xs text-slate-500 block">Kurir / Ekspedisi: {{ $stockIn->courier_or_driver_name ?? '-' }}</span>
            </div>

            <div>
                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Dokumen Pengadaan:</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-slate-500">No. PO:</span>
                    <span class="font-mono font-bold text-slate-800">{{ $stockIn->po_number ?? '-' }}</span>
                </div>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-slate-500">Surat Jalan (DO):</span>
                    <span class="font-mono font-bold text-emerald-700">{{ $stockIn->vendor_delivery_order_no ?? '-' }}</span>
                </div>
                <span class="text-xs text-slate-500 block mt-1">Petugas Penerima: <strong class="text-slate-700">{{ $stockIn->receivedBy->name }}</strong></span>
            </div>
        </div>

        @if($stockIn->notes)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-700">
                <span class="font-bold block mb-0.5">Catatan Gudang:</span>
                {{ $stockIn->notes }}
            </div>
        @endif

        <!-- ITEMS TABLE -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Perangkat / Material</th>
                        <th class="py-3 px-4 text-center">Qty Masuk</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-6">Serial Number Terdaftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stockIn->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->product->name }}</span>
                                <span class="font-mono text-xs text-slate-500">{{ $item->product->sku }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                {{ number_format($item->qty, 0) }} {{ $item->product->unit->abbreviation }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs">
                                Rp {{ number_format($item->cost_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-mono text-slate-700">
                                {{ $item->serial_numbers ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold text-sm text-slate-900 border-t border-slate-200">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right">Total Nilai Pembelian:</td>
                        <td class="py-3 px-4 text-right text-emerald-700 font-mono font-bold">
                            Rp {{ number_format($stockIn->total_cost, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

</div>
@endsection
