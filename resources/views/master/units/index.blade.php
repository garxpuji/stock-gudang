@extends('layouts.app')

@section('title', 'Master Satuan Ukur')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editUnit: {} }">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Master Data & Referensi</h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi satuan pengukuran kuantitas barang inventaris (Pcs, Meter, Roll, Set, dll).</p>
        </div>

        <a href="{{ route('units.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Satuan Baru</span>
        </a>
    </div>

    <!-- TABS NAVIGATION -->
    @include('master.tabs')

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('units.index') }}" class="flex items-center gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau singkatan satuan..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <button type="submit" class="py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm transition-colors cursor-pointer">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('units.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Reset">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Singkatan / Simbol</th>
                        <th class="py-3.5 px-4">Nama Lengkap Satuan</th>
                        <th class="py-3.5 px-4 text-center">Jumlah SKU Terdaftar</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($units as $unit)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-4 px-4 sm:px-6 font-mono font-bold text-slate-900">
                                <span class="bg-slate-100 px-3 py-1 rounded-md text-xs border border-slate-200 font-mono">
                                    {{ $unit->abbreviation }}
                                </span>
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-900">
                                {{ $unit->name }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $unit->products_count }} SKU
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('units.edit', $unit) }}" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    @if($unit->products_count === 0)
                                        <form method="POST" action="{{ route('units.destroy', $unit) }}" onsubmit="return confirm('Hapus satuan {{ addslashes($unit->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 18h12l3-18H3z"></path></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Belum ada satuan ukur</p>
                                    <p class="text-xs text-slate-500">Daftarkan satuan ukur baru (Unit, Pcs, Meter, Roll, Box, dll) untuk mulai mencatat inventaris.</p>
                                    <div class="pt-2">
                                        <a href="{{ route('units.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-xs transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            <span>Tambah Satuan Sekarang</span>
                                        </a>
                                    </div>
                                </div>
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

    <!-- CREATE MODAL -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="createModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form method="POST" action="{{ route('units.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-900">Tambah Satuan Ukur Baru</h3>
                        <button type="button" @click="createModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Singkatan / Simbol <span class="text-rose-500">*</span></label>
                        <input type="text" name="abbreviation" placeholder="Contoh: Unit, Pcs, Mtr, Roll, Box" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap Satuan <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" placeholder="Contoh: Pieces (Unit), Meter, Roll (Gulung)" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan Satuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="editModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200">
                <form :action="'{{ url('units') }}/' + editUnit.id" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-900">Ubah Satuan Ukur</h3>
                        <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Singkatan / Simbol <span class="text-rose-500">*</span></label>
                        <input type="text" name="abbreviation" x-model="editUnit.abbreviation" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap Satuan <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editUnit.name" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="editModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
