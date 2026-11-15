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
                    @foreach($products as $prod)
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
