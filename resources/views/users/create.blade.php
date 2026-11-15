@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tambah Pengguna Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan akun petugas baru untuk mengakses sistem logistik & gudang.</p>
        </div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-slate-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- FORM -->
    <form method="POST" action="{{ route('users.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 sm:p-8 space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

            <!-- Nama Lengkap -->
            <div class="sm:col-span-2">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Muhammad Fajar" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Email (Login) <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="nama@gayuhmedia.id" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>

            <!-- No. Telepon -->
            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    No. Handphone / WhatsApp
                </label>
                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
            </div>

            <!-- Role / Peran -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Peran / Hak Akses (Role) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Admin Gudang Card -->
                    <label class="relative flex items-start p-4 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer transition-colors has-checked:border-emerald-600 has-checked:bg-emerald-50/40">
                        <input type="radio" name="role" value="admin_gudang" {{ old('role', 'admin_gudang') === 'admin_gudang' ? 'checked' : '' }} class="mt-1 text-emerald-600 focus:ring-emerald-500">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-900">Admin Gudang</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Operasional stok masuk, barang keluar teknisi, retur, QC karantina, dan pencetakan surat jalan.</span>
                        </div>
                    </label>

                    <!-- Superadmin Card -->
                    <label class="relative flex items-start p-4 rounded-xl border border-slate-200 hover:border-purple-500 cursor-pointer transition-colors has-checked:border-purple-600 has-checked:bg-purple-50/40">
                        <input type="radio" name="role" value="superadmin" {{ old('role') === 'superadmin' ? 'checked' : '' }} class="mt-1 text-purple-600 focus:ring-purple-500">
                        <div class="ml-3">
                            <span class="block text-sm font-bold text-slate-900">Superadmin</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Akses penuh seluruh sistem: manajemen user, audit kartu stok, resolusi barang rusak, serta pengaturan global.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Kata Sandi (Password) <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password" id="password" placeholder="Minimal 6 karakter..." required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Ulangi Kata Sandi <span class="text-rose-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi kata sandi..." required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <!-- Status Aktif Toggle -->
            <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded-sm focus:ring-emerald-500 border-slate-300">
                    <div>
                        <span class="text-sm font-semibold text-slate-900 block">Akun Aktif Langsung</span>
                        <span class="text-xs text-slate-500 block">Pengguna dapat langsung login ke aplikasi setelah dibuat.</span>
                    </div>
                </label>
            </div>

        </div>

        <!-- BUTTONS -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-semibold text-sm transition-colors cursor-pointer">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Simpan Pengguna</span>
            </button>
        </div>
    </form>

</div>
@endsection
