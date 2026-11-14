<div class="flex items-center justify-between">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-9 h-9 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 font-bold text-sm shrink-0">
            {{ strtoupper(substr(Auth::user()?->name ?? 'Budi Santoso', 0, 2)) }}
        </div>
        <div class="truncate min-w-0">
            <span class="text-sm font-semibold text-slate-800 block truncate">{{ Auth::user()?->name ?? 'Budi Santoso' }}</span>
            <span class="text-xs text-slate-500 block capitalize truncate">{{ Auth::user()?->role ?? 'Admin Gudang' }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
        @csrf
        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer" title="Keluar / Logout">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </button>
    </form>
</div>
