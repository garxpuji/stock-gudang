@extends('layouts.app')

@section('title', 'Stok Opname Fisik Gudang')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Stok Opname Fisik</h1>
            <p class="text-sm text-slate-500 mt-1">Pencocokan stok fisik nyata di rak/bin gudang dengan angka saldo terdata di sistem database.</p>
        </div>

        <a href="{{ route('opname.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm shadow-xs transition-colors min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Input Penyesuaian Opname</span>
        </a>
    </div>

    <!-- SEARCH BAR -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('opname.index') }}" class="flex items-center gap-2.5">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama barang, SKU, barcode, atau serial number..." class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-hidden focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <button type="submit" class="px-4 py-2 min-h-[44px] bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl cursor-pointer">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('opname.index') }}" class="p-2.5 min-h-[44px] min-w-[44px] inline-flex items-center justify-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600" title="Reset Pencarian">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- TABEL REKONSILIASI STOK -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Perangkat / Material</th>
                        <th class="py-3.5 px-4">Lokasi Rak / Bin</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Stok Sistem (Aktif)</th>
                        <th class="py-3.5 px-4 text-right">Stok Karantina</th>
                        <th class="py-3.5 px-6 text-right">Aksi Opname</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $prod)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $prod->name }}</span>
                                <span class="font-mono text-xs text-slate-400">{{ $prod->sku }} &bull; {{ $prod->category?->name ?? 'Gudang' }}</span>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold text-slate-800">
                                {{ $prod->location?->name ?? 'Gudang Utama' }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border {{ $prod->status_badge }}">
                                    {{ $prod->status_label }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-right font-bold font-mono text-slate-900">
                                {{ $prod->formatted_stock }}
                            </td>
                            <td class="py-4 px-4 text-right font-mono text-xs text-orange-600">
                                {{ $prod->formatted_damaged_stock }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('opname.create', ['product_id' => $prod->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    <span>Sesuaikan Fisik</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                Tidak ada data barang yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="p-4 px-6 border-t border-slate-200 bg-slate-50/50">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
