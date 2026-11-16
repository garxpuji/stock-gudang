<div class="flex items-center justify-between gap-2">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 font-bold text-sm shrink-0">
            {{ strtoupper(substr(Auth::user()?->name ?? 'AD', 0, 2)) }}
        </div>
        <div class="truncate min-w-0">
            <span class="text-sm font-semibold text-slate-900 block truncate">{{ Auth::user()?->name ?? 'Petugas Gudang' }}</span>
            <span class="text-xs text-slate-600 block capitalize truncate">{{ str_replace('_', ' ', Auth::user()?->role ?? 'admin_gudang') }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
        @csrf
        <button type="submit" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center p-2 text-slate-600 hover:text-rose-700 rounded-lg hover:bg-rose-50 focus:outline-hidden focus:ring-2 focus:ring-rose-500 transition-colors cursor-pointer" title="Keluar Sistem" aria-label="Keluar Sistem">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </button>
    </form>
</div>
