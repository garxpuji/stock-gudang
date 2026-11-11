<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-emerald-100 border border-emerald-300 flex items-center justify-center text-emerald-800 font-bold text-sm">
            {{ strtoupper(substr(Auth::user()?->name ?? 'Admin', 0, 2)) }}
        </div>
        <div class="truncate">
            <span class="text-sm font-semibold text-slate-800 block truncate">{{ Auth::user()?->name ?? 'Budi Santoso' }}</span>
            <span class="text-xs text-slate-500 block capitalize truncate">{{ Auth::user()?->role ?? 'Admin Gudang' }}</span>
        </div>
    </div>

    @if(Auth::check())
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Keluar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Masuk</a>
    @endif
</div>
