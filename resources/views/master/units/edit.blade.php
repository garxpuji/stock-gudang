@extends('layouts.app')

@section('title', 'Ubah Satuan Ukur')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- BREADCRUMB / HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('units.index') }}" class="hover:text-emerald-600 transition-colors">Master Satuan</a>
                <span>&bull;</span>
                <span class="text-slate-800 font-semibold">Ubah Satuan</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Satuan: {{ $unit->name }}</h1>
        </div>

        <a href="{{ route('units.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- FORM -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8">
        <form method="POST" action="{{ route('units.update', $unit) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <!-- Singkatan / Simbol -->
                <div>
                    <label for="abbreviation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Singkatan / Simbol Satuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="abbreviation" id="abbreviation" value="{{ old('abbreviation', $unit->abbreviation) }}" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('abbreviation') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold min-h-[44px]">
                    @error('abbreviation')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Lengkap Satuan -->
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap Satuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $unit->name) }}" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('name') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('units.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors min-h-[44px] inline-flex items-center">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm shadow-xs transition-colors min-h-[44px] inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
