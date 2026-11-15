@extends('layouts.app')

@section('title', 'Penyesuaian Stok Opname')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{
    productId: '{{ $selectedProduct?->id ?? '' }}',
    currentStock: {{ $selectedProduct ? (float)$selectedProduct->stock : 0 }},
    currentDamaged: {{ $selectedProduct ? (float)$selectedProduct->damaged_stock : 0 }},
    actualStock: {{ $selectedProduct ? (float)$selectedProduct->stock : 0 }},
    actualDamaged: {{ $selectedProduct ? (float)$selectedProduct->damaged_stock : 0 }},
    unit: '{{ $selectedProduct?->unit?->abbreviation ?? '' }}',
    onProductChange(e) {
        const opt = e.target.options[e.target.selectedIndex];
        if (opt && opt.value) {
            this.currentStock = parseFloat(opt.getAttribute('data-stock') || 0);
            this.currentDamaged = parseFloat(opt.getAttribute('data-damaged') || 0);
            this.actualStock = this.currentStock;
            this.actualDamaged = this.currentDamaged;
            this.unit = opt.getAttribute('data-unit') || '';
        }
    }
}">

    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Penyesuaian Stok Opname Fisik</h1>
            <p class="text-sm text-slate-500 mt-1">Audit dan penyesuaian angka saldo gudang dengan hasil hitung fisik nyata.</p>
        </div>
        <a href="{{ route('opname.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Rekonsiliasi
        </a>
    </div>

    <form method="POST" action="{{ route('opname.store') }}" class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
        @csrf

        <!-- Pilih Produk -->
        <div>
            <label for="product_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Pilih Perangkat / Material <span class="text-rose-500">*</span></label>
            <select name="product_id" id="product_id" @change="onProductChange($event)" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                <option value="">-- Pilih Barang yang Dihitung Fisiknya --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" data-stock="{{ (float)$p->stock }}" data-damaged="{{ (float)$p->damaged_stock }}" data-unit="{{ $p->unit?->abbreviation ?? 'Unit' }}" {{ ($selectedProduct && $selectedProduct->id === $p->id) ? 'selected' : '' }}>
                        {{ $p->name }} [Stok Sistem: {{ $p->formatted_stock }} &bull; {{ $p->location?->code ?? 'Gudang' }}]
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Perbandingan Saldo -->
        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm">
            <div>
                <span class="text-xs text-slate-500 block font-semibold">Stok Aktif di Sistem:</span>
                <span class="text-lg font-bold text-slate-900 font-mono mt-0.5 block">
                    <span x-text="currentStock"></span> <span x-text="unit"></span>
                </span>
            </div>
            <div>
                <span class="text-xs text-slate-500 block font-semibold">Stok Rusak di Sistem:</span>
                <span class="text-lg font-bold text-orange-600 font-mono mt-0.5 block">
                    <span x-text="currentDamaged"></span> <span x-text="unit"></span>
                </span>
            </div>
        </div>

        <!-- Input Hasil Fisik Aktual -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="actual_stock" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Hasil Hitung Fisik Aktif <span class="text-rose-500">*</span>
                </label>
                <input type="number" step="any" min="0" name="actual_stock" id="actual_stock" x-model="actualStock" required class="w-full py-3 px-3.5 text-base bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono font-bold text-slate-900 min-h-[44px]">
                <span class="block text-xs text-slate-400 mt-1">
                    Selisih: <strong :class="(actualStock - currentStock) >= 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="(actualStock - currentStock) >= 0 ? '+' + (actualStock - currentStock) : (actualStock - currentStock)"></strong>
                </span>
            </div>

            <div>
                <label for="actual_damaged_stock" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Hasil Hitung Fisik Rusak (Karantina)
                </label>
                <input type="number" step="any" min="0" name="actual_damaged_stock" id="actual_damaged_stock" x-model="actualDamaged" class="w-full py-3 px-3.5 text-base bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono font-bold text-slate-900 min-h-[44px]">
            </div>
        </div>

        <!-- Penanggung Jawab & Dokumen Referensi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label for="person_in_charge" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Auditor / Petugas Fisik <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="person_in_charge" id="person_in_charge" value="{{ old('person_in_charge', Auth::user()?->name ?? 'Budi Santoso') }}" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
            </div>

            <div>
                <label for="reference_doc_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    No. Berita Acara / Dokumen Opname
                </label>
                <input type="text" name="reference_doc_no" id="reference_doc_no" value="{{ old('reference_doc_no', 'BA-OPN-' . date('Ym')) }}" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
            </div>
        </div>

        <!-- Alasan Penyesuaian -->
        <div>
            <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                Alasan / Catatan Penyesuaian Fisik <span class="text-rose-500">*</span>
            </label>
            <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Hasil stock opname bulanan, sisa kabel dropcore terpakai tanpa SPK, atau selisih fast connector pecah..." required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="{{ route('opname.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold shadow-xs">
                Simpan Penyesuaian Opname
            </button>
        </div>
    </form>

</div>
@endsection
