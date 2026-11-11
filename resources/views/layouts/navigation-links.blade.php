<div class="space-y-1">
    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Utama & Ringkasan</div>

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        <span>Dashboard Analitik</span>
    </a>

    <!-- Master Produk -->
    <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('products.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('products.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        <span>Katalog & Stok Barang</span>
    </a>
</div>

<div class="space-y-1 pt-3">
    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Operasional Gudang</div>

    <!-- Barang Keluar (Teknisi / Karyawan) -->
    <a href="{{ route('stock-out.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('stock-out.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('stock-out.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        <span class="flex-1">Barang Keluar</span>
        <span class="text-[10px] bg-sky-100 text-sky-800 font-semibold px-2 py-0.5 rounded-md">Teknisi</span>
    </a>

    <!-- Barang Kembali (Returns & QC) -->
    <a href="{{ route('returns.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('returns.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('returns.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18M13 8l4-4m0 0l4 4m-4-4v12"></path></svg>
        <span class="flex-1">Barang Kembali (Retur)</span>
        <span class="text-[10px] bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded-md">QC</span>
    </a>

    <!-- Barang Masuk (Inbound / Pengadaan) -->
    <a href="{{ route('stock-in.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('stock-in.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('stock-in.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
        <span>Barang Masuk (Vendor)</span>
    </a>

    <!-- Area Karantina & Barang Rusak -->
    <a href="{{ route('damaged-goods.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('damaged-goods.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('damaged-goods.*') ? 'text-orange-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <span class="flex-1">Barang Rusak & RMA</span>
        @php
            $pendingDmg = \App\Models\DamagedGood::where('action_taken', 'PENDING')->count();
        @endphp
        @if($pendingDmg > 0)
            <span class="text-[11px] bg-orange-100 text-orange-800 font-bold px-2 py-0.5 rounded-full">{{ $pendingDmg }}</span>
        @endif
    </a>
</div>

<div class="space-y-1 pt-3">
    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Audit & Rekonsiliasi</div>

    <!-- Unified Stock Ledger / Kartu Stok -->
    <a href="{{ route('mutations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('mutations.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('mutations.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span>Kartu Stok Terpadu</span>
    </a>

    <!-- Stok Opname -->
    <a href="{{ route('opname.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('opname.*') ? 'bg-emerald-50 text-emerald-800 font-semibold border border-emerald-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
        <svg class="w-5 h-5 {{ request()->routeIs('opname.*') ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        <span>Stok Opname Fisik</span>
    </a>
</div>
