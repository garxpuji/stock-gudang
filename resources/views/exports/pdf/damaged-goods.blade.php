@extends('exports.pdf.layout')

@section('title', 'Laporan Barang Rusak & Karantina')
@section('report_title', 'Laporan Barang Rusak & Unit Karantina (RMA / Write-Off)')

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
                <th style="width: 85px;">Kode Karantina</th>
                <th style="width: 75px;">Tanggal Lapor</th>
                <th style="width: 70px;">SKU</th>
                <th style="width: 125px;">Nama Perangkat</th>
                <th style="width: 85px;">Serial Number</th>
                <th style="width: 35px;" class="text-right">Qty</th>
                <th style="width: 80px;">Kategori Rusak</th>
                <th style="width: 85px;">Pelapor</th>
                <th style="width: 115px;">Gejala Kerusakan</th>
                <th style="width: 80px;" class="text-center">Keputusan</th>
                <th style="width: 80px;" class="text-right">Estimasi Rugi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $rowNo = 1; 
                $totalQty = 0; 
                $totalLoss = 0;
            @endphp
            @forelse($damagedGoods as $dmg)
                @php
                    $qty = (float)$dmg->qty;
                    $loss = (float)$dmg->loss_cost;
                    $totalQty += $qty;
                    $totalLoss += $loss;
                @endphp
                <tr>
                    <td class="text-center">{{ $rowNo++ }}</td>
                    <td class="font-mono" style="font-weight: bold; color: #ea580c;">{{ $dmg->code }}</td>
                    <td>{{ $dmg->reported_at->format('d/m/Y H:i') }}</td>
                    <td class="font-mono">{{ $dmg->product?->sku ?? '-' }}</td>
                    <td><strong>{{ $dmg->product?->name ?? '-' }}</strong></td>
                    <td class="font-mono" style="font-size: 6.5pt; color: #334155;">{{ $dmg->serial_number ?: '-' }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($qty, 0) }}</td>
                    <td>
                        <span class="badge badge-warning">{{ $dmg->damage_category?->value ?? '-' }}</span>
                    </td>
                    <td>
                        {{ $dmg->reported_by ?? '-' }}
                        <div style="font-size: 6.5pt; color: #64748b;">{{ $dmg->reporter_type?->value }}</div>
                    </td>
                    <td style="font-size: 7pt; color: #475569; font-style: italic;">
                        "{{ $dmg->damage_description }}"
                    </td>
                    <td class="text-center">
                        @if($dmg->action_taken?->value === 'Menunggu Keputusan' || $dmg->action_taken?->name === 'PENDING')
                            <span class="badge badge-danger">Karantina</span>
                        @else
                            <span class="badge badge-info">{{ $dmg->action_taken?->value }}</span>
                        @endif
                    </td>
                    <td class="text-right" style="color: #b91c1c; font-weight: bold;">
                        Rp {{ number_format($loss, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada catatan barang rusak/karantina yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="6" class="text-right" style="padding: 6px;">TOTAL UNIT RUSAK & ESTIMASI KERUGIAN</td>
                <td class="text-right" style="color: #ea580c;">{{ number_format($totalQty, 0) }}</td>
                <td colspan="4"></td>
                <td class="text-right" style="color: #b91c1c; font-size: 8pt;">Rp {{ number_format($totalLoss, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
