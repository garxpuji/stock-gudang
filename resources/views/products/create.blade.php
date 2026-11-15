@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Perangkat / Material ISP</h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan item baru ke dalam database master inventaris Gayuh Media Informatika.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Katalog
        </a>
    </div>

    <form method="POST" action="{{ route('products.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- SKU -->
            <div>
                <label for="sku" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kode SKU <span class="text-rose-500">*</span></label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" placeholder="Contoh: ONT-ZTE-F609" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>

            <!-- Barcode -->
            <div>
                <label for="barcode" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Barcode / EAN (Opsional)</label>
                <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="Scan atau ketik barcode..." class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>

            <!-- Nama Barang -->
            <div class="md:col-span-2">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Perangkat / Material <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: ONT GPON ZTE F609 V5.3 Dual Band AC1200" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <!-- Kategori -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="category_id" class="text-xs font-bold uppercase tracking-wider text-slate-700">Kategori ISP <span class="text-rose-500">*</span></label>
                    <a href="{{ route('categories.index') }}" target="_blank" class="text-[11px] text-emerald-600 font-semibold hover:underline">+ Tambah Kategori</a>
                </div>
                <select name="category_id" id="category_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Pilih Kategori...</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @if($categories->isEmpty())
                    <p class="text-[11px] text-rose-600 mt-1">Belum ada kategori terdaftar. Silakan <a href="{{ route('categories.index') }}" target="_blank" class="font-bold underline">buat kategori di sini</a> terlebih dahulu.</p>
                @endif
            </div>

            <!-- Satuan -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="unit_id" class="text-xs font-bold uppercase tracking-wider text-slate-700">Satuan Ukur <span class="text-rose-500">*</span></label>
                    <a href="{{ route('units.create') }}" target="_blank" class="text-[11px] text-emerald-600 font-semibold hover:underline">+ Tambah Satuan</a>
                </div>
                <select name="unit_id" id="unit_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Pilih Satuan...</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                    @endforeach
                </select>
                @if($units->isEmpty())
                    <p class="text-[11px] text-rose-600 mt-1">Belum ada satuan terdaftar. Silakan <a href="{{ route('units.create') }}" target="_blank" class="font-bold underline">buat satuan baru di sini</a> terlebih dahulu.</p>
                @endif
            </div>

            <!-- Lokasi Rak -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="location_id" class="text-xs font-bold uppercase tracking-wider text-slate-700">Lokasi Rak / Bin Gudang</label>
                    <a href="{{ route('locations.index') }}" target="_blank" class="text-[11px] text-emerald-600 font-semibold hover:underline">+ Tambah Rak</a>
                </div>
                <select name="location_id" id="location_id" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Pilih Lokasi Rak...</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Batas Minimum Stok -->
            <div>
                <label for="min_stock" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Batas Minimum (Safety Stock) <span class="text-rose-500">*</span></label>
                <input type="number" step="any" name="min_stock" id="min_stock" value="{{ old('min_stock', 5) }}" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>

            <!-- Estimasi Harga Beli -->
            <div>
                <label for="cost_price" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Estimasi Harga Beli (Rp)</label>
                <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>

            <!-- Flagging Serial Number -->
            <label for="has_serial_number" class="flex items-center gap-3 pt-6 cursor-pointer select-none group">
                <input type="hidden" name="has_serial_number" value="0">
                <input type="checkbox" name="has_serial_number" id="has_serial_number" value="1" {{ old('has_serial_number') ? 'checked' : '' }} class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                <span class="text-sm font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors cursor-pointer">
                    Wajib Catat Serial Number & MAC Address per unit (Perangkat Aktif)
                </span>
            </label>

            <!-- Spesifikasi Teknis -->
            <div class="md:col-span-2">
                <label for="specification" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Spesifikasi Teknis</label>
                <textarea name="specification" id="specification" rows="3" placeholder="Contoh: EPON/GPON Dual Band 2.4/5GHz, 4 GE, 1 POTS, 1 USB, redaman standar -8 s/d -25 dBm" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('specification') }}</textarea>
            </div>

            <!-- Stock Notes (Perlakuan Gudang) -->
            <div class="md:col-span-2">
                <label for="stock_notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Perlakuan & SOP Gudang</label>
                <textarea name="stock_notes" id="stock_notes" rows="2" placeholder="Contoh: Wajib uji redaman sebelum keluar ke teknisi PSB, simpan pada area kering dan tertutup dust cap" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('stock_notes') }}</textarea>
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-semibold shadow-xs">
                Simpan Barang
            </button>
        </div>
    </form>

</div>
@endsection
