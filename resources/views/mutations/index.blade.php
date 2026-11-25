@extends('layouts.app')

@section('title', 'Kartu Stok Terpadu (Stock Ledger)')

@section('content')
<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kartu Stok Terpadu (Unified Stock Ledger)</h1>
            <p class="text-sm text-slate-500 mt-1">Audit trail lengkap dan immutable atas seluruh pergerakan barang masuk, keluar ke teknisi, retur QC, dan karantina.</p>
        </div>

        @if(Auth::user()?->isAdminGudang())
            <div class="flex items-center gap-2">
                <a href="{{ route('opname.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Stok Opname</span>
                </a>
            </div>
        @endif
    </div>

    <!-- FILTER BAR TERPADU -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
        <form method="GET" action="{{ route('mutations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            
            <!-- Cari Umum -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Keterangan / PIC</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama teknisi, kurir, catatan..." class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Pilih Produk -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Barang</label>
                <select name="product_id" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Perangkat</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jenis Mutasi -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tipe Mutasi</label>
                <select name="mutation_type" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Mutasi</option>
                    @foreach($mutationTypes as $mt)
                        <option value="{{ $mt->value }}" {{ request('mutation_type') === $mt->value ? 'selected' : '' }}>{{ $mt->label() }}</option>
                    @endforeach
                </select>
            </div>

            <!-- No Tiket -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">No. Tiket / SPK</label>
                <input type="text" name="ticket_no" value="{{ request('ticket_no') }}" placeholder="SPK / TKT..." class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono focus:bg-white focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Tombol -->
            <div class="flex items-end">
                <button type="submit" class="w-full py-2 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl min-h-[40px]">
                    Terapkan Filter
                </button>
            </div>

        </form>
    </div>

    <!-- TABEL KARTU STOK -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Waktu Mutasi</th>
                        <th class="py-3.5 px-4">Nama Perangkat / Material</th>
                        <th class="py-3.5 px-4">Jenis Transaksi</th>
                        <th class="py-3.5 px-4 text-right">Kuantitas</th>
                        <th class="py-3.5 px-4 text-center">Saldo Perubahan</th>
                        <th class="py-3.5 px-4">Pihak Fisik (Teknisi/Kurir)</th>
                        <th class="py-3.5 px-4">Ref. Tiket/SPK</th>
                        <th class="py-3.5 px-6">Keterangan Lengkap</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($mutations as $mut)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                <span class="font-semibold text-slate-800 block">{{ $mut->created_at->format('d/m/Y') }}</span>
                                <span class="font-mono text-slate-400 block">{{ $mut->created_at->format('H:i:s') }} WIB</span>
                            </td>

                            <td class="py-4 px-4">
                                <a href="{{ route('products.show', $mut->product) }}" class="font-bold text-slate-900 hover:text-emerald-600 block">
                                    {{ $mut->product->name }}
                                </a>
                                <span class="font-mono text-xs text-slate-400">{{ $mut->product->sku }}</span>
                            </td>

                            <td class="py-4 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $mut->mutation_type->badge() }}">
                                    {{ $mut->mutation_type->label() }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-right font-mono font-bold whitespace-nowrap text-sm {{ $mut->qty < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $mut->qty > 0 ? '+'.number_format($mut->qty, 0) : number_format($mut->qty, 0) }}
                                <span class="text-xs text-slate-400 font-normal">{{ $mut->product?->unit?->abbreviation ?? '' }}</span>
                            </td>

                            <td class="py-4 px-4 text-center font-mono text-xs whitespace-nowrap">
                                <span class="text-slate-400">{{ number_format($mut->previous_stock, 0) }}</span>
                                <span class="text-slate-300 mx-1">&rarr;</span>
                                <span class="font-bold text-slate-900">{{ number_format($mut->current_stock, 0) }}</span>
                            </td>

                            <td class="py-4 px-4 text-xs font-bold text-slate-900 whitespace-nowrap">
                                {{ $mut->person_in_charge }}
                            </td>

                            <td class="py-4 px-4 text-xs font-mono font-bold text-emerald-700 whitespace-nowrap">
                                {{ $mut->reference_ticket_no ?? '-' }}
                            </td>

                            <td class="py-4 px-6 text-xs text-slate-600 max-w-xs" title="{{ $mut->remarks }}">
                                <span class="block line-clamp-2">{{ $mut->remarks }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Operator: {{ $mut->systemOperator->name }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500">Tidak ada riwayat mutasi sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutations->hasPages())
            <div class="p-4 px-6 border-t border-slate-200 bg-slate-50/50">
                {{ $mutations->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
