@extends('layouts.app')

@section('title', 'Master Satuan Ukur')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Master Satuan Ukur</h1>
            <p class="text-sm text-slate-600 mt-0.5">Daftar satuan hitung inventaris gudang (Unit, Pcs, Meter, Roll, Box).</p>
        </div>

        <a href="{{ route('units.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm transition-colors min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Tambah Satuan</span>
        </a>
    </div>

    @include('master.tabs')

    <div class="bg-white p-4 rounded-xl border border-slate-200">
        <form method="GET" action="{{ route('units.index') }}" class="flex items-center gap-2.5">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau singkatan satuan..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <button type="submit" class="py-2 px-4 min-h-[44px] rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm transition-colors cursor-pointer">
                Cari
            </button>
            @if(request('search'))
            <a href="{{ route('units.index') }}" class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors" title="Reset Pencarian">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
            @endif
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Singkatan</th>
                        <th class="py-3.5 px-4">Nama Satuan</th>
                        <th class="py-3.5 px-4 text-center">Jumlah SKU</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($units as $unit)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3.5 px-4 sm:px-6 font-mono font-bold text-slate-900">
                            <span class="bg-slate-100 px-3 py-1 rounded-md text-xs border border-slate-200 font-mono">
                                {{ $unit->abbreviation }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            {{ $unit->name }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                {{ $unit->products_count }} SKU
                            </span>
                        </td>
                        <td class="py-3.5 px-4 sm:px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('units.edit', $unit) }}" class="inline-flex items-center gap-1 px-3 py-1.5 min-h-[38px] text-xs font-semibold text-slate-700 hover:text-emerald-800 bg-slate-100 hover:bg-emerald-50 rounded-lg border border-slate-200 transition-colors" title="Ubah Satuan">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </a>

                                @if($unit->products_count === 0)
                                <form method="POST" action="{{ route('units.destroy', $unit) }}" onsubmit="return confirm('Hapus satuan {{ addslashes($unit->name) }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 min-h-[38px] text-xs font-semibold text-rose-700 hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-200 transition-colors cursor-pointer" title="Hapus Satuan">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 px-4 text-center text-sm text-slate-600">
                            Belum ada satuan ukur. Klik tombol <strong>Tambah Satuan</strong> di atas untuk membuat satuan baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($units->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $units->links() }}
        </div>
        @endif
    </div>

</div>
@endsection