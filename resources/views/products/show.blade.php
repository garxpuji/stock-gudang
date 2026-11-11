@extends('layouts.app')

@section('title', $product->name . ' - Detail Barang')

@section('content')
<div class="space-y-6">

    <!-- BREADCRUMB & HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('products.index') }}" class="hover:text-emerald-600">Katalog Barang</a>
                <span>/</span>
                <span>{{ $product->category->name }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $product->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $product->status_badge }}">
                    {{ $product->status_label }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('opname.create', ['product_id' => $product->id]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                <span>Stok Opname</span>
            </a>
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                <span>Ubah Master</span>
            </a>
        </div>
    </div>

    <!-- KPI CARDS DETAIL STOK -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Aktif Tersedia</span>
            <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $product->formatted_stock }}</span>
            <span class="text-xs text-slate-400 mt-0.5 block">Siap didistribusikan ke teknisi</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-orange-700 uppercase tracking-wider block">Stok Karantina / Rusak</span>
            <span class="text-2xl font-bold text-orange-600 mt-1 block">{{ $product->formatted_damaged_stock }}</span>
            <span class="text-xs text-slate-400 mt-0.5 block">Menunggu RMA / Scrapped</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Batas Minimum (Safety)</span>
            <span class="text-2xl font-bold text-slate-800 mt-1 block">{{ number_format($product->min_stock) }} {{ $product->unit?->abbreviation }}</span>
            <span class="text-xs text-slate-400 mt-0.5 block">Threshold notifikasi restock</span>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Estimasi Harga Beli</span>
            <span class="text-2xl font-bold text-slate-800 mt-1 block">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
            <span class="text-xs text-slate-400 mt-0.5 block">Per {{ $product->unit?->name }}</span>
        </div>
    </div>

    <!-- INFORMASI TEKNIS & LOKASI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Spesifikasi & Stock Notes -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900">Spesifikasi Teknis ISP</h2>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-sm text-slate-700 leading-relaxed font-sans">
                    {{ $product->specification ?? 'Belum ada catatan spesifikasi teknis.' }}
                </div>

                <h3 class="text-sm font-bold text-slate-900 pt-2">Keterangan & Prosedur Handling Gudang</h3>
                <div class="bg-emerald-50/50 rounded-xl p-4 border border-emerald-200/80 text-sm text-emerald-950 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <span class="font-semibold block mb-0.5">Instruksi Petugas Gudang:</span>
                        {{ $product->stock_notes ?? 'Penyimpanan standar sesuai panduan logistik umum.' }}
                    </div>
                </div>
            </div>

            <!-- TABEL SERIAL NUMBER JIKA PERANGKAT AKTIF -->
            @if($product->has_serial_number)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                    <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Inventaris Serial Number & MAC Address</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Pelacakan unit fisik per Serial Number (Active Tracking)</p>
                        </div>
                        <span class="text-xs font-semibold bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full border border-emerald-200">
                            {{ $product->serials->count() }} Unit Terdata
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="py-3 px-6">Serial Number</th>
                                    <th class="py-3 px-4">MAC Address</th>
                                    <th class="py-3 px-4 text-center">Status Unit</th>
                                    <th class="py-3 px-4">Penerima Lapangan</th>
                                    <th class="py-3 px-4">Ref. Tiket / SPK</th>
                                    <th class="py-3 px-6">Catatan Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($product->serials as $sn)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-3.5 px-6 font-mono font-bold text-slate-900">{{ $sn->serial_number }}</td>
                                        <td class="py-3.5 px-4 font-mono text-slate-600">{{ $sn->mac_address ?? '-' }}</td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-semibold border {{ $sn->status->badge() }}">
                                                {{ $sn->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 font-medium text-slate-800">{{ $sn->last_recipient ?? '-' }}</td>
                                        <td class="py-3.5 px-4 font-mono text-emerald-700 font-semibold">{{ $sn->last_ticket_no ?? '-' }}</td>
                                        <td class="py-3.5 px-6 text-slate-500 truncate max-w-xs" title="{{ $sn->notes }}">{{ $sn->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-6 text-center text-slate-400">Belum ada unit serial number terdaftar. Tambahkan via Transaksi Barang Masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        <!-- Kolom Kanan: Identitas Master & Lokasi Rak -->
        <div class="space-y-6">
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Informasi Master</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Kode SKU:</span>
                        <span class="font-mono font-bold text-slate-900">{{ $product->sku }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Barcode / EAN:</span>
                        <span class="font-mono text-slate-800">{{ $product->barcode ?? '-' }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Kategori ISP:</span>
                        <span class="font-semibold text-slate-800">{{ $product->category->name }}</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Satuan Ukur:</span>
                        <span class="font-semibold text-slate-800">{{ $product->unit->name }} ({{ $product->unit->abbreviation }})</span>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block">Lokasi Penyimpanan Rak / Bin:</span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-100 text-slate-800 font-semibold border border-slate-200 mt-1">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            {{ $product->location?->name ?? 'Gudang Utama' }} ({{ $product->location?->code ?? 'MAIN' }})
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Box -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 space-y-3">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Aksi Cepat Logistik</h3>
                
                <a href="{{ route('stock-out.create') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Serah Terima ke Teknisi</span>
                </a>

                <a href="{{ route('stock-in.create') }}" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                    <span>Tambah Stok Masuk (Vendor)</span>
                </a>
            </div>

        </div>

    </div>

    <!-- RIWAYAT MUTASI STOK PRODUK INI -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Histori Kartu Mutasi Produk</h2>
                <p class="text-xs text-slate-500 mt-0.5">Audit log mutasi penambahan, pengeluaran, dan retur</p>
            </div>
            <a href="{{ route('mutations.index', ['product_id' => $product->id]) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                Buka di Kartu Stok Terpadu &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-6">Tanggal & Waktu</th>
                        <th class="py-3 px-4">Jenis Mutasi</th>
                        <th class="py-3 px-4 text-right">Kuantitas</th>
                        <th class="py-3 px-4 text-right">Saldo Sesudah</th>
                        <th class="py-3 px-4">Pihak Terlibat Fisik</th>
                        <th class="py-3 px-4">Ref. Tiket / Dokumen</th>
                        <th class="py-3 px-6">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentMutations as $mut)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-3 px-6 font-medium text-slate-800">{{ $mut->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-md font-semibold text-[10px] border {{ $mut->mutation_type->badge() }}">
                                    {{ $mut->mutation_type->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ $mut->qty < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $mut->qty > 0 ? '+'.number_format($mut->qty, 0) : number_format($mut->qty, 0) }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">{{ number_format($mut->current_stock, 0) }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-800">{{ $mut->person_in_charge }}</td>
                            <td class="py-3 px-4 font-mono text-emerald-700 font-semibold">{{ $mut->reference_ticket_no ?? '-' }}</td>
                            <td class="py-3 px-6 text-slate-500">{{ $mut->remarks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">Belum ada mutasi stok tercatat untuk barang ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
