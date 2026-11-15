<div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-px">
    <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap {{ request()->routeIs('categories.*') ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50 rounded-t-xl' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
        <span>Kategori Barang</span>
    </a>

    <a href="{{ route('units.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap {{ request()->routeIs('units.*') ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50 rounded-t-xl' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 18h12l3-18H3z"></path></svg>
        <span>Satuan Ukur</span>
    </a>

    <a href="{{ route('locations.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap {{ request()->routeIs('locations.*') ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50 rounded-t-xl' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        <span>Lokasi Rak / Bin</span>
    </a>

    <a href="{{ route('suppliers.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap {{ request()->routeIs('suppliers.*') ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50 rounded-t-xl' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        <span>Supplier / Vendor</span>
    </a>
</div>
