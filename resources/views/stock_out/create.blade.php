@extends('layouts.app')

@section('title', 'Loket Pengeluaran Barang')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="outboundForm()">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Loket Pengeluaran Barang</h1>
            <p class="text-sm text-slate-600 mt-0.5">Catat pengambilan perangkat atau material oleh teknisi dan staf.</p>
        </div>
        <a href="{{ route('stock-out.index') }}" class="inline-flex items-center px-3 py-2 min-h-[44px] text-sm font-semibold text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100">
            Riwayat Keluar
        </a>
    </div>

    <form method="POST" action="{{ route('stock-out.store') }}" class="space-y-5">
        @csrf

        <!-- KARTU 1: SIAPA YANG MENGAMBIL -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2.5">1. Data Pengambil Barang</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tipe Penerima -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pihak Penerima <span class="text-rose-600">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 min-h-[44px] rounded-xl border cursor-pointer transition-colors" :class="recipientType === 'TEKNISI' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-semibold' : 'border-slate-200 bg-white text-slate-700'">
                            <input type="radio" name="recipient_type" value="TEKNISI" x-model="recipientType" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm">Teknisi Lapangan</span>
                        </label>

                        <label class="flex items-center gap-2.5 p-3 min-h-[44px] rounded-xl border cursor-pointer transition-colors" :class="recipientType === 'KARYAWAN' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-semibold' : 'border-slate-200 bg-white text-slate-700'">
                            <input type="radio" name="recipient_type" value="KARYAWAN" x-model="recipientType" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm">Karyawan Internal</span>
                        </label>
                    </div>
                </div>

                <!-- Nama Pengambil -->
                <div>
                    <label for="picked_up_by" class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Pengambil <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="picked_up_by" id="picked_up_by" value="{{ old('picked_up_by') }}" placeholder="Nama teknisi atau staf" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- Tim / Divisi -->
                <div>
                    <label for="recipient_department_or_team" class="block text-xs font-bold text-slate-700 mb-1">
                        Tim / Divisi <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="recipient_department_or_team" id="recipient_department_or_team" value="{{ old('recipient_department_or_team') }}" placeholder="Misal: Tim Fiber 1 atau NOC" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- Tujuan Keperluan -->
                <div class="sm:col-span-2">
                    <label for="purpose" class="block text-xs font-bold text-slate-700 mb-1">
                        Keperluan Pemakaian <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="purpose" id="purpose" value="{{ old('purpose') }}" placeholder="Misal: Pasang Baru Pelanggan atau Perbaikan Kabel" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <button type="button" @click="document.getElementById('purpose').value = 'Pasang Baru (PSB) Pelanggan FTTH'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 min-h-[36px] rounded-lg cursor-pointer">PSB Pelanggan</button>
                        <button type="button" @click="document.getElementById('purpose').value = 'Perbaikan Gangguan Jaringan'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 min-h-[36px] rounded-lg cursor-pointer">Perbaikan Gangguan</button>
                        <button type="button" @click="document.getElementById('purpose').value = 'Peminjaman Alat Kerja'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 min-h-[36px] rounded-lg cursor-pointer">Peminjaman Alat</button>
                    </div>
                </div>
            </div>

            <!-- KOLOM OPSIONAL (DILIPAT AGAR FORM TETAP RINGKAS) -->
            <details class="pt-2 border-t border-slate-100 group">
                <summary class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer py-2 select-none list-none flex items-center gap-1.5">
                    <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>Isi detail tambahan (No. SPK/Tiket, No. Telepon, Waktu, Catatan)</span>
                </summary>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3">
                    <div>
                        <label for="reference_ticket_no" class="block text-xs font-semibold text-slate-700 mb-1">No. SPK / Tiket</label>
                        <input type="text" name="reference_ticket_no" id="reference_ticket_no" value="{{ old('reference_ticket_no') }}" placeholder="Nomor tiket / SPK" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono min-h-[44px]">
                    </div>
                    <div>
                        <label for="recipient_phone" class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / WA</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="Nomor kontak" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                    <div>
                        <label for="transaction_date" class="block text-xs font-semibold text-slate-700 mb-1">Waktu Keluar</label>
                        <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d\TH:i')) }}" required class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="keterangan_pengeluaran" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <input type="text" name="keterangan_pengeluaran" id="keterangan_pengeluaran" value="{{ old('keterangan_pengeluaran') }}" placeholder="Catatan kelengkapan adaptor atau kondisi fisik" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                </div>
            </details>
        </div>

        <!-- KARTU 2: BARANG YANG DIAMBIL -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">2. Barang yang Diserahkan</h2>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-xs border border-emerald-200 min-h-[44px] cursor-pointer">
                    <span>+ Tambah Barang</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                            <!-- Pilih Barang -->
                            <div class="sm:col-span-8">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang <span class="text-rose-600">*</span></label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" @change="onProductChange(index)" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-stock="{{ (float)$p->stock }}" data-unit="{{ $p->unit?->abbreviation ?? 'Unit' }}" data-has-sn="{{ $p->has_serial_number ? '1' : '0' }}">
                                        {{ $p->name }} (Stok: {{ $p->formatted_stock }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Jumlah -->
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Jumlah <span x-text="item.unit ? '(' + item.unit + ')' : ''"></span> <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" step="any" min="0.01" :max="item.max_stock || null" :name="'items[' + index + '][qty]'" x-model="item.qty" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl font-mono font-bold min-h-[44px]">
                            </div>

                            <!-- Hapus Baris -->
                            <div class="sm:col-span-1 flex sm:justify-end sm:pt-6">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-semibold px-2 cursor-pointer" title="Hapus Baris">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Pilih Serial Number jika perangkat ber-SN -->
                        <div x-show="item.has_sn" class="pt-2 border-t border-slate-200">
                            <label class="block text-xs font-semibold text-slate-800 mb-1.5">
                                Pilih Serial Number (Centang sesuai jumlah keluar)
                            </label>
                            <div class="max-h-36 overflow-y-auto bg-white p-2.5 rounded-xl border border-slate-200 space-y-1">
                                <template x-for="sn in getAvailableSerials(item.product_id)" :key="sn.id">
                                    <label class="flex items-center gap-2.5 p-2 hover:bg-slate-50 rounded-lg cursor-pointer text-xs min-h-[40px]">
                                        <input type="checkbox" :name="'items[' + index + '][serial_ids][]'" :value="sn.id" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                        <span class="font-mono font-bold text-slate-900" x-text="sn.serial_number"></span>
                                        <span class="text-slate-500 font-mono" x-show="sn.mac_address" x-text="'(' + sn.mac_address + ')'"></span>
                                    </label>
                                </template>
                                <div x-show="getAvailableSerials(item.product_id).length === 0" class="text-xs text-amber-800 py-1.5 px-2">
                                    Tidak ada Serial Number berstatus tersedia untuk barang ini.
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- TOMBOL SIMPAN -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('stock-out.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-sm font-semibold min-h-[44px] inline-flex items-center">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold transition-colors min-h-[44px] inline-flex items-center gap-2 cursor-pointer">
                <span>Simpan &amp; Cetak Bukti</span>
            </button>
        </div>
    </form>

</div>

@php
$serialsMap = $products->where('has_serial_number', true)->mapWithKeys(function ($p) {
return [
(string) $p->id => $p->availableSerials->map(fn ($sn) => [
'id' => $sn->id,
'serial_number' => $sn->serial_number,
'mac_address' => $sn->mac_address ?? '',
])->values()->all(),
];
});
@endphp
<script id="outbound-serials-json" type="application/json">@json($serialsMap)</script>

@push('scripts')
<script>
    const allProductsSerials = JSON.parse(document.getElementById('outbound-serials-json')?.textContent || '{}');

    function outboundForm() {
        return {
            recipientType: 'TEKNISI',
            items: [{
                product_id: '',
                qty: 1,
                unit: '',
                max_stock: 0,
                has_sn: false
            }],
            addItem() {
                this.items.push({
                    product_id: '',
                    qty: 1,
                    unit: '',
                    max_stock: 0,
                    has_sn: false
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            onProductChange(index) {
                const selectEl = document.querySelectorAll("select[name*='[product_id]']")[index];
                if (!selectEl) return;
                const opt = selectEl.options[selectEl.selectedIndex];
                if (opt && opt.value) {
                    this.items[index].max_stock = parseFloat(opt.getAttribute('data-stock') || 0);
                    this.items[index].unit = opt.getAttribute('data-unit') || '';
                    this.items[index].has_sn = opt.getAttribute('data-has-sn') === '1';
                } else {
                    this.items[index].max_stock = 0;
                    this.items[index].unit = '';
                    this.items[index].has_sn = false;
                }
            },
            getAvailableSerials(productId) {
                if (!productId || !allProductsSerials[productId]) return [];
                return allProductsSerials[productId];
            }
        };
    }
</script>
@endpush
@endsection