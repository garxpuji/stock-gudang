@extends('layouts.app')

@section('title', 'Master Supplier & Distributor')

@section('content')
<div class="space-y-6" x-data="{ createModal: false, editModal: false, editSupplier: {} }">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Master Data & Referensi</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar mitra distributor, vendor pengadaan perangkat, dan kontak darurat suplier.</p>
        </div>

        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Supplier Baru</span>
        </a>
    </div>

    <!-- TABS NAVIGATION -->
    @include('master.tabs')

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('suppliers.index') }}" class="flex items-center gap-3">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vendor, kontak, atau nomor telepon..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <button type="submit" class="py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm transition-colors cursor-pointer">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Reset">
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
                        <th class="py-3.5 px-4 sm:px-6">Nama Vendor / Perusahaan</th>
                        <th class="py-3.5 px-4">Kontak Person</th>
                        <th class="py-3.5 px-4">Telepon & Email</th>
                        <th class="py-3.5 px-4 text-center">Transaksi Masuk</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($suppliers as $sup)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-4 px-4 sm:px-6">
                                <span class="font-bold text-slate-900 block">{{ $sup->name }}</span>
                                @if($sup->code)
                                    <span class="font-mono text-xs text-slate-400 block">{{ $sup->code }}</span>
                                @endif
                                @if($sup->address)
                                    <span class="text-xs text-slate-500 block truncate max-w-xs mt-0.5">{{ $sup->address }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 font-semibold text-slate-800">
                                {{ $sup->contact_name ?? '-' }}
                            </td>
                            <td class="py-4 px-4 text-xs font-mono text-slate-600">
                                <div>{{ $sup->phone ?? '-' }}</div>
                                <div class="text-slate-400">{{ $sup->email ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $sup->stock_ins_count }} PO Masuk
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" @click="editSupplier = {{ json_encode($sup) }}; editModal = true" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors cursor-pointer" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>

                                    @if($sup->stock_ins_count === 0)
                                        <form method="POST" action="{{ route('suppliers.destroy', $sup) }}" onsubmit="return confirm('Hapus supplier {{ addslashes($sup->name) }}?');" class="inline">
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
                            <td colspan="5" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Belum ada data supplier / vendor</p>
                                    <p class="text-xs text-slate-500">Klik tombol di atas untuk mendaftarkan mitra vendor pengadaan barang.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE MODAL -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity" @click="createModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form method="POST" action="{{ route('suppliers.store') }}" class="p-6 space-y-4">
                    @csrf
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-900">Tambah Supplier Baru</h3>
                        <button type="button" @click="createModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Perusahaan Vendor <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" placeholder="Contoh: PT Fiberhome Technologies Indonesia" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kode Vendor</label>
                            <input type="text" name="code" placeholder="SUP-001" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama PIC / Kontak</label>
                            <input type="text" name="contact_name" placeholder="Nama Marketing / PIC" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="phone" placeholder="021-xxxx / 0812xxx" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" placeholder="sales@vendor.com" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Kantor / Gudang Vendor</label>
                        <textarea name="address" rows="2" placeholder="Alamat lengkap..." class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="createModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold">Simpan Supplier</button>
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
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                <form :action="'{{ url('suppliers') }}/' + editSupplier.id" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-lg font-bold text-slate-900">Ubah Data Supplier</h3>
                        <button type="button" @click="editModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Perusahaan Vendor <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" x-model="editSupplier.name" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Kode Vendor</label>
                            <input type="text" name="code" x-model="editSupplier.code" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama PIC / Kontak</label>
                            <input type="text" name="contact_name" x-model="editSupplier.contact_name" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="phone" x-model="editSupplier.phone" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" x-model="editSupplier.email" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Alamat Kantor / Gudang Vendor</label>
                        <textarea name="address" rows="2" x-model="editSupplier.address" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl"></textarea>
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
