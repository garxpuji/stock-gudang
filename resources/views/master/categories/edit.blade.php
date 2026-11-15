@extends('layouts.app')

@section('title', 'Ubah Kategori Barang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- BREADCRUMB / HEADER -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('categories.index') }}" class="hover:text-emerald-600 transition-colors">Master Kategori</a>
                <span>&bull;</span>
                <span class="text-slate-800 font-semibold">Ubah Kategori</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Ubah Kategori: {{ $category->name }}</h1>
        </div>

        <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- FORM -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="code" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kode Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $category->code) }}" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('code') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono font-bold min-h-[44px]">
                    @error('code')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border @error('name') border-rose-300 ring-2 ring-rose-100 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                    @error('name')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Keterangan (Opsional)
                    </label>
                    <textarea name="description" id="description" rows="2" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('categories.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold transition-colors min-h-[44px] inline-flex items-center">
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
