@extends('exports.pdf.layout')

@section('title', 'Laporan Stok Tersedia')
@section('report_title', 'Laporan Stok Tersedia & Katalog Gudang')

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
                <th style="width: 75px;">SKU</th>
                <th style="width: 140px;">Nama Perangkat / Material</th>
                <th style="width: 90px;">Kategori</th>
                <th style="width: 75px;">Lokasi Rak</th>
                <th style="width: 45px;" class="text-right">Fisik</th>
                <th style="width: 40px;" class="text-right">Rusak</th>
                <th style="width: 40px;" class="text-right">Min</th>
                <th style="width: 35px;" class="text-center">Sat</th>
                <th style="width: 60px;" class="text-center">Status</th>
                <th style="width: 35px;" class="text-center">SN</th>
                <th style="width: 80px;" class="text-right">Harga Beli</th>
                <th style="width: 90px;" class="text-right">Nilai Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalStock = 0; 
                $totalDamaged = 0; 
                $totalAssetValue = 0; 
            @endphp
            @forelse($products as $index => $prod)
                @php
                    $stock = (float)$prod->stock;
                    $damaged = (float)$prod->damaged_stock;
                    $assetVal = $stock * (float)$prod->cost_price;
                    $totalStock += $stock;
                    $totalDamaged += $damaged;
                    $totalAssetValue += $assetVal;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono" style="font-weight: bold;">{{ $prod->sku }}</td>
                    <td>
                        <strong>{{ $prod->name }}</strong>
                        @if($prod->barcode)
                            <div style="font-size: 6.5pt; color: #64748b;">Barcode: {{ $prod->barcode }}</div>
                        @endif
                    </td>
                    <td>{{ $prod->category?->name ?? '-' }}</td>
                    <td>{{ $prod->location?->name ?? 'Gudang Utama' }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($stock, 0) }}</td>
                    <td class="text-right" style="color: {{ $damaged > 0 ? '#b91c1c' : '#64748b' }};">{{ number_format($damaged, 0) }}</td>
                    <td class="text-right" style="color: #64748b;">{{ number_format($prod->min_stock, 0) }}</td>
                    <td class="text-center">{{ $prod->unit?->abbreviation ?? '-' }}</td>
                    <td class="text-center">
                        @if($stock <= 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($stock <= (float)$prod->min_stock)
                            <span class="badge badge-warning">Menipis</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $prod->has_serial_number ? 'Ya' : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($prod->cost_price, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($assetVal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 15px; color: #94a3b8;">
                        Tidak ada data stok barang yang sesuai dengan filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-right" style="padding: 6px;">TOTAL KESELURUHAN ({{ count($products) }} SKU)</td>
                <td class="text-right">{{ number_format($totalStock, 0) }}</td>
                <td class="text-right" style="color: #b91c1c;">{{ number_format($totalDamaged, 0) }}</td>
                <td colspan="4"></td>
                <td class="text-right" style="color: #059669; font-size: 8pt;" colspan="2">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
@endsection
