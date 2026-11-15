@extends('layouts.app')

@section('title', 'Bukti Serah Terima - ' . $stockOut->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- TOP ACTIONS -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('stock-out.index') }}" class="hover:text-emerald-600">Riwayat Outbound</a>
                <span>/</span>
                <span class="font-mono text-emerald-700 font-bold">{{ $stockOut->transaction_code }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Pengeluaran Barang</h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('stock-out.print', $stockOut) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-semibold text-sm shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Dokumen Resmi</span>
            </a>
            <a href="{{ route('stock-out.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Buat Baru</span>
            </a>
        </div>
    </div>

    <!-- DOCUMENT CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
        
        <!-- Header Dokumen Resmi -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-slate-200 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold shadow-xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">Gayuh Media Informatika</h2>
                    <span class="text-xs text-slate-500 block">Internet Service Provider & Optical Network Infrastructure</span>
                    <span class="text-xs font-semibold text-emerald-700 block mt-0.5">BUKTI PENGELUARAN BARANG / MATERIAL GUDANG</span>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="font-mono text-base font-bold text-emerald-700 block">{{ $stockOut->transaction_code }}</span>
                <span class="text-xs text-slate-500 block">Waktu: {{ $stockOut->transaction_date->format('d F Y, H:i') }} WIB</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border mt-1 {{ $stockOut->recipient_type->badge() }}">
                    {{ $stockOut->recipient_type->label() }}
                </span>
            </div>
        </div>

        <!-- Detail Identitas Lapangan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200 text-sm">
            <div>
                <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Yang Mengambil Secara Fisik:</span>
                <span class="font-bold text-slate-900 text-base block mt-0.5">{{ $stockOut->picked_up_by }}</span>
                <span class="text-xs text-slate-600 block mt-0.5">{{ $stockOut->recipient_department_or_team }}</span>
                @if($stockOut->recipient_phone)
                    <span class="text-xs text-slate-500 font-mono block mt-0.5">Kontak: {{ $stockOut->recipient_phone }}</span>
                @endif
            </div>

            <div>
                <span class="text-xs text-slate-400 block font-semibold uppercase tracking-wider">Keperluan & Dokumen Referensi:</span>
                <span class="font-bold text-slate-900 block mt-0.5">{{ $stockOut->purpose }}</span>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-slate-500">Ref / SPK / Tiket:</span>
                    <span class="font-mono font-bold text-emerald-700">{{ $stockOut->reference_ticket_no ?? '-' }}</span>
                </div>
                <span class="text-xs text-slate-500 block mt-1">Admin Penyerah: <strong class="text-slate-700">{{ $stockOut->dispatchedBy->name }}</strong></span>
            </div>
        </div>

        @if($stockOut->keterangan_pengeluaran)
            <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-200/80 text-xs text-amber-950">
                <span class="font-bold block mb-0.5">Catatan Pengeluaran:</span>
                {{ $stockOut->keterangan_pengeluaran }}
            </div>
        @endif

        <!-- TABEL BARANG YANG DIKELUARKAN -->
        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Perangkat / Material</th>
                        <th class="py-3 px-4">Kode SKU</th>
                        <th class="py-3 px-4 text-center">Kuantitas</th>
                        <th class="py-3 px-6">Detail Serial Number / MAC Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($stockOut->items as $idx => $item)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->product?->name ?? 'Produk Dihapus' }}</span>
                                <span class="text-xs text-slate-500">{{ $item->product?->category?->name ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-700 font-semibold">{{ $item->product?->sku ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-900">
                                {{ number_format($item->qty, 0) }} {{ $item->product?->unit?->abbreviation ?? '' }}
                            </td>
                            <td class="py-3.5 px-6">
                                @if($item->serials->count() > 0)
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($item->serials as $sn)
                                            <span class="inline-flex items-center gap-1 font-mono text-xs font-bold bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-md border border-emerald-200">
                                                <span>{{ $sn->serial_number }}</span>
                                                @if($sn->mac_address)
                                                    <span class="text-slate-400 font-normal">({{ $sn->mac_address }})</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Non-Serial (Material/Konsumsi)</span>
                                @endif
                                @if($item->notes)
                                    <span class="block text-xs text-slate-500 mt-1 italic">{{ $item->notes }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
