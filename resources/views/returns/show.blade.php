@extends('layouts.app')

@section('title', 'Bukti Retur - ' . $return->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- TOP ACTIONS -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('returns.index') }}" class="hover:text-emerald-600">Riwayat Retur</a>
                <span>/</span>
                <span class="font-mono text-emerald-700 font-bold">{{ $return->transaction_code }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Penerimaan Barang Kembali</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('returns.print', $return) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-semibold text-sm shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Bukti Retur</span>
            </a>
            <a href="{{ route('returns.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Input Retur Baru</span>
            </a>
        </div>
    </div>

    <!-- DOCUMENT CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
        
        <!-- Header Dokumen -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M13 8l4-4m0 0l4 4m-4-4v12"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">Gayuh Media Informatika</h2>
                    <span class="text-xs text-slate-500 block">Warehouse Inspection & Quality Control Division</span>
                    <span class="text-xs font-semibold text-emerald-700 block mt-0.5">BUKTI PENERIMAAN BARANG KEMBALI (RETUR)</span>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="font-mono text-base font-bold text-emerald-700 block">{{ $return->transaction_code }}</span>
                <span class="text-xs text-slate-500 block">Waktu: {{ $return->transaction_date->format('d F Y, H:i') }} WIB</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border mt-1 bg-slate-100 text-slate-700 border-slate-200">
                    {{ $return->return_category }}
                </span>
            </div>
        </div>

        <!-- Detail Pengembali & Admin -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-sm">
            <div>
                <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Yang Mengembalikan Fisik:</span>
                <span class="font-bold text-slate-900 text-base block mt-0.5">{{ $return->returned_by }}</span>
                <span class="text-xs text-slate-600 block mt-0.5">{{ $return->returned_by_contact ?? $return->recipient_type->value }}</span>
            </div>

            <div>
                <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Referensi & Pemeriksa:</span>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-slate-500">Ref / SPK / Tiket:</span>
                    <span class="font-mono font-bold text-emerald-700">{{ $return->reference_ticket_no ?? '-' }}</span>
                </div>
                <span class="text-xs text-slate-500 block mt-1">Admin QC Penerima: <strong class="text-slate-700">{{ $return->receivedBy->name }}</strong></span>
            </div>
        </div>

        @if($return->keterangan_pengembalian)
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-700">
                <span class="font-bold block mb-0.5 text-slate-800">Alasan / Catatan Pengembalian:</span>
                {{ $return->keterangan_pengembalian }}
            </div>
        @endif

        <!-- TABEL BARANG YANG DIKEMBALIKAN DENGAN HASIL QC -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Perangkat / Material</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-center">Hasil Inspeksi QC</th>
                        <th class="py-3 px-4">Serial Number</th>
                        <th class="py-3 px-6">Tindakan Alokasi & Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($return->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->product->name }}</span>
                                <span class="font-mono text-xs text-slate-500">{{ $item->product->sku }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                {{ number_format($item->qty, 0) }} {{ $item->product->unit->abbreviation }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $item->qc_status->badge() }}">
                                    {{ $item->qc_status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-800">
                                @if($item->serial_number)
                                    <span class="font-bold text-slate-900">{{ $item->serial_number }}</span>
                                    @if($item->mac_address)
                                        <span class="block text-slate-400 font-normal">({{ $item->mac_address }})</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                @if($item->qc_status === \App\Enums\QcStatus::GOOD)
                                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Restocked ke Stok Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-bold text-orange-700">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Masuk Area Karantina Rusak
                                    </span>
                                @endif
                                <p class="text-slate-500 mt-1 italic">{{ $item->qc_notes }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
