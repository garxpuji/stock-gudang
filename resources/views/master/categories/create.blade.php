@extends('layouts.app')

@section('title', 'Tambah Kategori Barang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('categories.index') }}" class="hover:text-emerald-600 transition-colors">Master Kategori</a>
                <span>&bull;</span>
                <span class="text-slate-800 font-semibold">Tambah Kategori</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Kategori Barang</h1>
            <p class="text-sm text-slate-500 mt-0.5">Klasifikasi jenis perangkat dan material infrastruktur jaringan ISP.</p>
        </div>

        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Quick Presets -->
    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
        <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Pilihan Cepat Kategori ISP:</span>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="document.getElementById('code').value='ONT'; document.getElementById('name').value='Perangkat Aktif ONT'; document.getElementById('description').value='Optical Network Terminal / Modem Pelanggan GPON/XPON';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Perangkat ONT
            </button>
            <button type="button" onclick="document.getElementById('code').value='ROUTER'; document.getElementById('name').value='Router & Wireless AP'; document.getElementById('description').value='MikroTik, Access Point, Wireless Bridge';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Router & AP
            </button>
            <button type="button" onclick="document.getElementById('code').value='KABEL-FO'; document.getElementById('name').value='Kabel Dropcore & Fiber Optic'; document.getElementById('description').value='Kabel optik 1 core, 2 core, 24 core, ADSS';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Kabel Dropcore
            </button>
            <button type="button" onclick="document.getElementById('code').value='PASIF-FO'; document.getElementById('name').value='Material Pasif FO'; document.getElementById('description').value='Splitter, ODP, ODC, Adapter, Patchcord, Pigtail';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Aksesoris Pasif FO
            </button>
            <button type="button" onclick="document.getElementById('code').value='ALAT-UKUR'; document.getElementById('name').value='Peralatan & Tool Lapangan'; document.getElementById('description').value='Fusion Splicer, OTDR, OPM, Visual Fault Locator';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Tool & Splicer
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-6">
            @csrf
            @if(request('redirect_to'))
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
            @endif

            <div class="space-y-4">
                <div>
                    <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kode Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Contoh: ONT, ROUTER, KABEL-FO" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('code') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono font-bold min-h-[44px]">
                    @error('code')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Perangkat Aktif ONT" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('name') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Keterangan (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="2" placeholder="Deskripsi peruntukan kategori..." class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors min-h-[44px] inline-flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm shadow-xs transition-colors min-h-[44px] inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Kategori</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
