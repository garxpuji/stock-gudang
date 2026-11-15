@extends('layouts.app')

@section('title', 'Tambah Lokasi Rak Gudang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('locations.index') }}" class="hover:text-emerald-600 transition-colors">Master Lokasi</a>
                <span>&bull;</span>
                <span class="text-slate-800 font-semibold">Tambah Rak Baru</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Lokasi Rak Gudang</h1>
            <p class="text-sm text-slate-500 mt-0.5">Penetapan koordinat penyimpanan fisik barang di gudang logistik.</p>
        </div>

        <a href="{{ route('locations.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Quick Presets -->
    <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
        <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Pilihan Cepat Posisi Rak:</span>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="document.getElementById('code').value='RAK-A1-01'; document.getElementById('name').value='Rak A1 - Perangkat ONT & Modem'; document.getElementById('description').value='Lorong A, Rak 1 Tingkat 1 - Penyimpanan ONT segel';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Rak A1 (ONT)
            </button>
            <button type="button" onclick="document.getElementById('code').value='RAK-B1-01'; document.getElementById('name').value='Rak B1 - Routerboard & SFP'; document.getElementById('description').value='Lorong B, Rak 1 - Switch, SFP, Router';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Rak B1 (Router/SFP)
            </button>
            <button type="button" onclick="document.getElementById('code').value='AREA-KABEL-01'; document.getElementById('name').value='Area Drum Kabel & Dropcore'; document.getElementById('description').value='Lantai 1 Area Pallet Khusus Roll Kabel Dropcore 1000m';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Area Kabel Dropcore
            </button>
            <button type="button" onclick="document.getElementById('code').value='LEMARI-TOOL'; document.getElementById('name').value='Lemari Besi Alat Splicer & OTDR'; document.getElementById('description').value='Lemari khusus berpendingin untuk alat ukur presisi';" class="text-xs font-mono font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-lg border border-emerald-200 hover:bg-emerald-600 hover:text-white transition-colors cursor-pointer shadow-2xs">
                + Lemari Splicer/OTDR
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8">
        <form method="POST" action="{{ route('locations.store') }}" class="space-y-6">
            @csrf
            @if(request('redirect_to'))
                <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
            @endif

            <div class="space-y-4">
                <div>
                    <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kode Rak / Bin <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" placeholder="Contoh: RAK-A1-01, BIN-B2, AREA-KBL" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('code') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono font-bold min-h-[44px]">
                    @error('code')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lokasi Rak <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Rak Utama A1 - Perangkat ONT" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('name') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Keterangan / Panduan Posisi (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="2" placeholder="Catatan posisi lorong, lantai, atau petunjuk akses..." class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('locations.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors min-h-[44px] inline-flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm shadow-xs transition-colors min-h-[44px] inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Lokasi Rak</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
