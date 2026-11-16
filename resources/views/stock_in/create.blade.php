@extends('layouts.app')

@section('title', 'Penerimaan Barang Masuk')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="inboundForm()">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Penerimaan Barang Masuk (Inbound)</h1>
            <p class="text-sm text-slate-600 mt-0.5">Tambahkan stok masuk dari supplier atau pengadaan baru.</p>
        </div>
        <a href="{{ route('stock-in.index') }}" class="inline-flex items-center px-3 py-2 min-h-[44px] text-sm font-semibold text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100">
            Riwayat Masuk
        </a>
    </div>

    <form method="POST" action="{{ route('stock-in.store') }}" class="space-y-5">
        @csrf

        <!-- KARTU 1: SUPPLIER & TANGGAL -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2.5">1. Asal Pengadaan</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="supplier_id" class="block text-xs font-bold text-slate-700 mb-1">Supplier / Distributor</label>
                    <select name="supplier_id" id="supplier_id" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                        <option value="">-- Pilih Supplier Terdaftar --</option>
                        @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="transaction_date" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Masuk <span class="text-rose-600">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>
            </div>

            <!-- DETAIL PENGIRIMAN OPSIONAL (DILIPAT) -->
            <details class="pt-2 border-t border-slate-100 group">
                <summary class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer py-2 select-none list-none flex items-center gap-1.5">
                    <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>Isi detail dokumen pengiriman (Nama Vendor Manual, No. PO, Surat Jalan, Kurir, Catatan)</span>
                </summary>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3">
                    <div>
                        <label for="supplier_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Vendor Manual (Jika belum terdaftar)</label>
                        <input type="text" name="supplier_name" id="supplier_name" placeholder="Nama toko atau vendor" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                    <div>
                        <label for="po_number" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Purchase Order (PO)</label>
                        <input type="text" name="po_number" id="po_number" placeholder="Nomor PO" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono min-h-[44px]">
                    </div>
                    <div>
                        <label for="vendor_delivery_order_no" class="block text-xs font-semibold text-slate-700 mb-1">No. Surat Jalan (DO)</label>
                        <input type="text" name="vendor_delivery_order_no" id="vendor_delivery_order_no" placeholder="Nomor surat jalan vendor" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono min-h-[44px]">
                    </div>
                    <div>
                        <label for="courier_or_driver_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kurir / Ekspedisi</label>
                        <input type="text" name="courier_or_driver_name" id="courier_or_driver_name" placeholder="Nama pengantar atau ekspedisi" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Penerimaan</label>
                        <input type="text" name="notes" id="notes" placeholder="Catatan kondisi barang saat diterima" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                </div>
            </details>
        </div>

        <!-- KARTU 2: DAFTAR BARANG MASUK -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">2. Daftar Barang Masuk</h2>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-xs border border-emerald-200 min-h-[44px] cursor-pointer">
                    <span>+ Tambah Barang</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                            <div class="sm:col-span-6">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Barang <span class="text-rose-600">*</span></label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} [{{ $p->sku }}]</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah <span class="text-rose-600">*</span></label>
                                <input type="number" step="any" min="0.01" :name="'items[' + index + '][qty]'" x-model="item.qty" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl font-mono font-bold min-h-[44px]">
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                                <input type="number" step="any" :name="'items[' + index + '][cost_price]'" x-model="item.cost_price" placeholder="0" class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl font-mono min-h-[44px]">
                            </div>

                            <div class="sm:col-span-1 flex sm:justify-end sm:pt-6">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-semibold px-2 cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Serial Number Baru (Opsional, pisahkan dengan koma)</label>
                            <input type="text" :name="'items[' + index + '][serial_numbers]'" placeholder="Ketik atau scan SN jika perangkat aktif" class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg font-mono min-h-[40px]">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('stock-in.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-sm font-semibold min-h-[44px] inline-flex items-center">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold transition-colors min-h-[44px] inline-flex items-center cursor-pointer">
                Simpan Barang Masuk
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function inboundForm() {
        return {
            items: [{
                product_id: '',
                qty: 1,
                cost_price: ''
            }],
            addItem() {
                this.items.push({
                    product_id: '',
                    qty: 1,
                    cost_price: ''
                });
            },
            removeItem(index) {
                if (this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            }
        };
    }
</script>
@endpush
@endsection