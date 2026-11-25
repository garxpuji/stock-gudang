@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Tambah Barang Baru</h1>
            <p class="text-sm text-slate-600 mt-0.5">Daftarkan jenis perangkat atau material baru ke katalog gudang.</p>
        </div>
        <a href="{{ route('products.index') }}" class="inline-flex items-center px-3 py-2 min-h-[44px] text-sm font-semibold text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100">
            Kembali ke Katalog
        </a>
    </div>

    <form method="POST" action="{{ route('products.store') }}" class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- SKU -->
            <div>
                <label for="sku" class="block text-xs font-bold text-slate-700 mb-1">Kode SKU <span class="text-rose-600">*</span></label>
                <input type="text" name="sku" id="sku" value="{{ old('sku') }}" placeholder="Misal: ONT-ZTE-F609" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
            </div>

            <!-- Batas Minimum Stok -->
            <div>
                <label for="min_stock" class="block text-xs font-bold text-slate-700 mb-1">Batas Minimum Stok <span class="text-rose-600">*</span></label>
                <input type="number" step="any" name="min_stock" id="min_stock" value="{{ old('min_stock', 5) }}" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
            </div>

            <!-- Nama Barang -->
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Barang / Perangkat <span class="text-rose-600">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Nama lengkap perangkat atau material" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
            </div>

            <!-- Kategori -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="category_id" class="text-xs font-bold text-slate-700">Kategori <span class="text-rose-600">*</span></label>
                    <a href="{{ route('categories.create', ['redirect_to' => route('products.create', [], false)]) }}" class="text-xs text-emerald-700 font-semibold hover:underline">+ Buat Kategori</a>
                </div>
                <select name="category_id" id="category_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Satuan -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="unit_id" class="text-xs font-bold text-slate-700">Satuan Ukur <span class="text-rose-600">*</span></label>
                    <a href="{{ route('units.create', ['redirect_to' => route('products.create', [], false)]) }}" class="text-xs text-emerald-700 font-semibold hover:underline">+ Buat Satuan</a>
                </div>
                <select name="unit_id" id="unit_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <option value="">-- Pilih Satuan --</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Lokasi Rak -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="location_id" class="text-xs font-bold text-slate-700">Lokasi Rak</label>
                    <a href="{{ route('locations.create', ['redirect_to' => route('products.create', [], false)]) }}" class="text-xs text-emerald-700 font-semibold hover:underline">+ Buat Rak</a>
                </div>
                <select name="location_id" id="location_id" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <option value="">-- Pilih Lokasi Rak --</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Flagging Serial Number -->
            <div class="flex items-end">
                <label for="has_serial_number" class="flex items-center gap-3 p-3 min-h-[44px] w-full rounded-xl border border-slate-200 bg-slate-50 cursor-pointer select-none">
                    <input type="hidden" name="has_serial_number" value="0">
                    <input type="checkbox" name="has_serial_number" id="has_serial_number" value="1" {{ old('has_serial_number') ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span class="text-xs font-semibold text-slate-800">Wajib catat Serial Number per unit</span>
                </label>
            </div>
        </div>

        <!-- DETAIL OPSIONAL (DILIPAT) -->
        <details class="pt-2 border-t border-slate-100 group">
            <summary class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer py-2 select-none list-none flex items-center gap-1.5">
                <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span>Isi detail tambahan (Barcode, Harga Beli, Spesifikasi, Catatan Gudang)</span>
            </summary>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3">
                <div>
                    <label for="barcode" class="block text-xs font-semibold text-slate-700 mb-1">Barcode / EAN</label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="Nomor barcode" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono min-h-[44px]">
                </div>
                <div>
                    <label for="cost_price" class="block text-xs font-semibold text-slate-700 mb-1">Harga Beli Satuan (Rp)</label>
                    <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono min-h-[44px]">
                </div>
                <div class="sm:col-span-2">
                    <label for="specification" class="block text-xs font-semibold text-slate-700 mb-1">Spesifikasi Teknis</label>
                    <textarea name="specification" id="specification" rows="2" placeholder="Spesifikasi singkat perangkat" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl">{{ old('specification') }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label for="stock_notes" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Penyimpanan</label>
                    <input type="text" name="stock_notes" id="stock_notes" value="{{ old('stock_notes') }}" placeholder="Petunjuk penyimpanan di rak" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                </div>
            </div>
        </details>

        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold min-h-[44px] inline-flex items-center">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold min-h-[44px] inline-flex items-center cursor-pointer">
                Simpan Barang
            </button>
        </div>
    </form>

</div>
@endsection