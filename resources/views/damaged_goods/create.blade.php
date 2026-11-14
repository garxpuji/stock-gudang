@extends('layouts.app')

@section('title', 'Lapor Kerusakan Langsung')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pelaporan Barang Rusak / Cacat</h1>
            <p class="text-sm text-slate-500 mt-1">Pencatatan langsung kerusakan perangkat lapangan (tersambar petir, port terbakar, atau insiden handling gudang).</p>
        </div>
        <a href="{{ route('damaged-goods.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Area Karantina
        </a>
    </div>

    <form method="POST" action="{{ route('damaged-goods.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <!-- Pilih Produk -->
            <div class="md:col-span-2">
                <label for="product_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Perangkat / Material yang Rusak <span class="text-rose-500">*</span></label>
                <select name="product_id" id="product_id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <option value="">-- Pilih Perangkat --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} [Stok: {{ $p->formatted_stock }} &bull; {{ $p->category->name }}]</option>
                    @endforeach
                </select>
            </div>

            <!-- Qty -->
            <div>
                <label for="qty" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kuantitas Rusak <span class="text-rose-500">*</span></label>
                <input type="number" step="any" min="0.01" name="qty" id="qty" value="{{ old('qty', 1) }}" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono font-bold min-h-[44px]">
            </div>

            <!-- Kategori Kerusakan -->
            <div>
                <label for="damage_category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kategori Kerusakan <span class="text-rose-500">*</span></label>
                <select name="damage_category" id="damage_category" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <option value="Tersambar Petir / Overvoltage">Tersambar Petir / Overvoltage</option>
                    <option value="Port LAN/Fiber Rusak">Port LAN/Fiber Rusak</option>
                    <option value="Optic Laser Drop">Optic Laser Drop</option>
                    <option value="Fisik Pecah / Cacat">Fisik Pecah / Cacat</option>
                    <option value="Mati Total">Mati Total</option>
                </select>
            </div>

            <!-- Serial Number -->
            <div>
                <label for="serial_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Serial Number Fisik (Jika ada)</label>
                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" placeholder="Contoh: ZTEGCA881008" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
            </div>

            <!-- MAC Address -->
            <div>
                <label for="mac_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">MAC Address (Jika ada)</label>
                <input type="text" name="mac_address" id="mac_address" value="{{ old('mac_address') }}" placeholder="Contoh: 48:D2:24:AB:12:34" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
            </div>

            <!-- Nama Pelapor -->
            <div>
                <label for="reported_by" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Pelapor / Teknisi <span class="text-rose-500">*</span></label>
                <input type="text" name="reported_by" id="reported_by" value="{{ old('reported_by') }}" placeholder="Nama Teknisi atau Admin" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
            </div>

            <!-- Tipe Pelapor -->
            <div>
                <label for="reporter_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pihak Pelapor <span class="text-rose-500">*</span></label>
                <select name="reporter_type" id="reporter_type" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <option value="TEKNISI">Teknisi Lapangan</option>
                    <option value="KARYAWAN">Karyawan / Gudang Internal</option>
                </select>
            </div>

            <!-- Opsi Potong Stok Aktif -->
            <label for="deduct_from_active_stock" class="md:col-span-2 p-4 rounded-xl bg-slate-50 hover:bg-slate-100/80 border border-slate-200 flex items-center gap-3 cursor-pointer select-none transition-colors group">
                <input type="hidden" name="deduct_from_active_stock" value="0">
                <input type="checkbox" name="deduct_from_active_stock" id="deduct_from_active_stock" value="1" class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 cursor-pointer">
                <span class="text-xs font-semibold text-slate-800 group-hover:text-emerald-700 transition-colors cursor-pointer">
                    Potong langsung dari Saldo Stok Aktif Gudang (Pilih jika barang rusak saat disimpan di gudang atau saat pengujian pre-provisioning)
                </span>
            </label>

            <!-- Deskripsi Kerusakan -->
            <div class="md:col-span-2">
                <label for="damage_description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Keterangan Gejala Kerusakan Teknis <span class="text-rose-500">*</span></label>
                <textarea name="damage_description" id="damage_description" rows="3" placeholder="Jelaskan secara detail: indikasi bau terbakar, port LAN mati, redaman drop, fisik pecah, dll..." required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('damage_description') }}</textarea>
            </div>

            <!-- Upload Foto Bukti -->
            <div class="md:col-span-2">
                <label for="photo_proof" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Foto Kondisi Fisik Perangkat</label>
                <input type="file" name="photo_proof" id="photo_proof" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
            </div>

        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('damaged-goods.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white text-sm font-semibold shadow-xs">
                Laporkan ke Karantina
            </button>
        </div>
    </form>

</div>
@endsection
