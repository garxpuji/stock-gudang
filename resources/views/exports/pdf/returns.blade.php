@extends('exports.pdf.layout')

@section('title', 'Laporan Barang Kembali')
@section('report_title', 'Laporan Barang Kembali (Retur & Hasil QC)')

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
                <th style="width: 85px;">No. Retur</th>
                <th style="width: 75px;">Tanggal</th>
                <th style="width: 95px;">Teknisi / Pengembali</th>
                <th style="width: 80px;">Kategori Retur</th>
                <th style="width: 75px;">Ref. Tiket</th>
                <th style="width: 70px;">SKU</th>
                <th style="width: 120px;">Nama Perangkat / Material</th>
                <th style="width: 40px;" class="text-right">Qty</th>
                <th style="width: 65px;" class="text-center">Hasil QC</th>
                <th style="width: 75px;">Tindakan</th>
                <th style="width: 95px;">Serial Number / MAC</th>
                <th style="width: 80px;">Petugas QC</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $rowNo = 1; 
                $totalQty = 0; 
            @endphp
            @forelse($returns as $ret)
                @foreach($ret->items as $item)
                    @php $totalQty += (float)$item->qty; @endphp
                    <tr>
                        <td class="text-center">{{ $rowNo++ }}</td>
                        <td class="font-mono" style="font-weight: bold; color: #0284c7;">{{ $ret->transaction_code }}</td>
                        <td>{{ $ret->transaction_date->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $ret->returned_by }}</strong>
                            @if($ret->returned_by_contact)
                                <div style="font-size: 6.5pt; color: #64748b;">{{ $ret->returned_by_contact }}</div>
                            @endif
                        </td>
                        <td>{{ $ret->return_category ?? '-' }}</td>
                        <td class="font-mono">{{ $ret->reference_ticket_no ?? '-' }}</td>
                        <td class="font-mono">{{ $item->product?->sku ?? '-' }}</td>
                        <td>{{ $item->product?->name ?? '-' }}</td>
                        <td class="text-right" style="font-weight: bold;">
                            {{ number_format($item->qty, 0) }} {{ $item->product?->unit?->abbreviation }}
                        </td>
                        <td class="text-center">
                            @if($item->qc_status?->value === 'GOOD')
                                <span class="badge badge-success">Bagus / Siap</span>
                            @elseif($item->qc_status?->value === 'DAMAGED')
                                <span class="badge badge-danger">Rusak</span>
                            @else
                                <span class="badge badge-warning">Perlu Perbaikan</span>
                            @endif
                        </td>
                        <td style="font-size: 7pt;">{{ $item->action_taken ?? '-' }}</td>
                        <td class="font-mono" style="font-size: 6.5pt; color: #334155;">
                            {{ $item->serial_number ?: ($item->mac_address ?: '-') }}
                        </td>
                        <td>{{ $ret->receivedBy?->name ?? 'Admin QC' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada transaksi barang kembali yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="8" class="text-right" style="padding: 6px;">TOTAL UNIT BARANG KEMBALI</td>
                <td class="text-right" style="color: #0284c7;">{{ number_format($totalQty, 0) }}</td>
                <td colspan="4"></td>
            </tr>
        </tfoot>
    </table>
@endsection
