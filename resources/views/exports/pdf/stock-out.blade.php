@extends('exports.pdf.layout')

@section('title', 'Laporan Barang Keluar')
@section('report_title', 'Laporan Barang Keluar (Distribusi Teknisi)')

@section('filters')
    @if(!empty($activeFilters))
        <div class="filters-banner">
            <strong>Filter Diterapkan:</strong> {{ implode(' • ', $activeFilters) }}
        </div>
    @endif
@endsection

@section('content')
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 90px;">No. Transaksi</th>
                <th style="width: 80px;">Tanggal</th>
                <th style="width: 100px;">Penerima / Teknisi</th>
                <th style="width: 85px;">Divisi / Tim</th>
                <th style="width: 85px;">No. Tiket / SPK</th>
                <th style="width: 75px;">SKU</th>
                <th style="width: 130px;">Nama Perangkat / Material</th>
                <th style="width: 45px;" class="text-right">Qty</th>
                <th style="width: 35px;" class="text-center">Sat</th>
                <th style="width: 110px;">Serial Number</th>
                <th style="width: 80px;">Petugas Loket</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $rowNo = 1; 
                $totalQty = 0; 
            @endphp
            @forelse($transactions as $tx)
                @foreach($tx->items as $item)
                    @php
                        $sns = $item->serials->pluck('serial_number')->implode(', ');
                        if (empty($sns) && !empty($item->notes) && str_contains($item->notes, 'SN:')) {
                            $sns = $item->notes;
                        }
                        $totalQty += (float)$item->qty;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $rowNo++ }}</td>
                        <td class="font-mono" style="font-weight: bold; color: #047857;">{{ $tx->transaction_code }}</td>
                        <td>{{ $tx->transaction_date->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $tx->picked_up_by }}</strong>
                            <div style="font-size: 6.5pt; color: #64748b;">{{ $tx->recipient_type->value }}</div>
                        </td>
                        <td>{{ $tx->recipient_department_or_team ?? '-' }}</td>
                        <td class="font-mono">{{ $tx->reference_ticket_no ?? '-' }}</td>
                        <td class="font-mono">{{ $item->product?->sku ?? '-' }}</td>
                        <td>{{ $item->product?->name ?? '-' }}</td>
                        <td class="text-right" style="font-weight: bold;">{{ number_format($item->qty, 0) }}</td>
                        <td class="text-center">{{ $item->product?->unit?->abbreviation ?? '-' }}</td>
                        <td class="font-mono" style="font-size: 6.5pt; color: #334155;">{{ $sns ?: '-' }}</td>
                        <td>{{ $tx->dispatchedBy?->name ?? 'Admin Gudang' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada transaksi pengeluaran barang yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="8" class="text-right" style="padding: 6px;">TOTAL UNIT/BARANG KELUAR</td>
                <td class="text-right" style="color: #047857;">{{ number_format($totalQty, 0) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
@endsection
