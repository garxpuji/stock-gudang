@extends('layouts.app')

@section('title', 'Kelola Pengguna Sistem')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTION -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kelola Pengguna Sistem</h1>
            <p class="text-sm text-slate-500 mt-1">Manajemen akun petugas, teknisi, dan administrator sistem logistik gudang.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('users.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                <span>Tambah User Baru</span>
            </a>
        </div>
    </div>

    <!-- METRICS CARDS -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- 1. Total User -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Akun</span>
                <span class="text-2xl font-bold text-slate-900 mt-0.5 block">{{ $totalUsers }}</span>
            </div>
        </div>

        <!-- 2. Superadmin -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Superadmin</span>
                <span class="text-2xl font-bold text-slate-900 mt-0.5 block">{{ $superadminCount }}</span>
            </div>
        </div>

        <!-- 3. Admin Gudang -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Admin Gudang</span>
                <span class="text-2xl font-bold text-slate-900 mt-0.5 block">{{ $adminGudangCount }}</span>
            </div>
        </div>

        <!-- 4. User Aktif -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">User Aktif</span>
                <span class="text-2xl font-bold text-slate-900 mt-0.5 block">{{ $activeCount }}</span>
            </div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Keyword -->
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, no. HP..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <select name="role" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Peran / Role</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="admin_gudang" {{ request('role') == 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">Semua Status Akun</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <!-- Tombol Filter -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold text-sm transition-colors cursor-pointer">
                    Filter Data
                </button>
                @if(request()->anyFilled(['search', 'role', 'status']))
                    <a href="{{ route('users.index') }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="Reset Filter">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- USERS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 sm:px-6">Pengguna</th>
                        <th scope="col" class="py-3.5 px-4">Kontak / No. HP</th>
                        <th scope="col" class="py-3.5 px-4">Peran (Role)</th>
                        <th scope="col" class="py-3.5 px-4">Status</th>
                        <th scope="col" class="py-3.5 px-4">Terdaftar</th>
                        <th scope="col" class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <!-- Pengguna -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $user->role === 'superadmin' ? 'bg-purple-100 text-purple-800 border-purple-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200' }} border flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <span class="font-bold text-slate-900 block truncate">{{ $user->name }}</span>
                                        <span class="text-xs text-slate-500 font-mono block truncate">{{ $user->email }}</span>
                                        @if(Auth::id() === $user->id)
                                            <span class="inline-flex items-center text-[10px] text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded font-semibold mt-0.5">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kontak -->
                            <td class="py-4 px-4 text-slate-700 font-mono text-xs">
                                {{ $user->phone ?? '-' }}
                            </td>

                            <!-- Peran (Role) -->
                            <td class="py-4 px-4">
                                @if($user->role === 'superadmin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                        Superadmin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Admin Gudang
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="w-3.5 h-3.5 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Terdaftar -->
                            <td class="py-4 px-4 text-xs text-slate-500">
                                {{ $user->created_at?->format('d M Y') ?? '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('users.edit', $user) }}" class="p-1.5 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors cursor-pointer" title="Edit Pengguna">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    @if(Auth::id() !== $user->id)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna {{ addslashes($user->name) }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus Pengguna">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-700">Tidak ada pengguna ditemukan</p>
                                    <p class="text-xs text-slate-500">Coba ubah kata kunci pencarian atau reset filter yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
