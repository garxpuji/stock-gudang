@extends('layouts.app')

@section('title', 'Penerimaan Barang Masuk (Inbound)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="inboundForm()">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Penerimaan Barang Masuk (Inbound)</h1>
            <p class="text-sm text-slate-500 mt-1">Input data pengadaan dari supplier, nomor surat jalan vendor, dan registrasi serial number baru.</p>
        </div>
        <a href="{{ route('stock-in.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Riwayat Inbound
        </a>
    </div>

    <form method="POST" action="{{ route('stock-in.store') }}" class="space-y-6">
        @csrf

        <!-- INFORMASI PENGIRIMAN & VENDOR -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-5">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Dokumen Pengadaan & Vendor</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Supplier / Distributor</label>
                    <select name="supplier_id" id="supplier_id" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                        <option value="">-- Pilih Supplier Resmi --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Supplier Name Manual -->
                <div>
                    <label for="supplier_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Vendor (Jika tidak di list)</label>
                    <input type="text" name="supplier_name" id="supplier_name" placeholder="Nama perusahaan supplier..." class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- Nomor PO -->
                <div>
                    <label for="po_number" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nomor Purchase Order (PO)</label>
                    <input type="text" name="po_number" id="po_number" placeholder="Contoh: PO-GMI-202611-001" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
                </div>

                <!-- Surat Jalan Vendor -->
                <div>
                    <label for="vendor_delivery_order_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">No. Surat Jalan Vendor (DO)</label>
                    <input type="text" name="vendor_delivery_order_no" id="vendor_delivery_order_no" placeholder="Contoh: SJ-ZTE-2026-9912" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
                </div>

                <!-- Kurir / Driver -->
                <div>
                    <label for="courier_or_driver_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Nama Kurir / Pengemudi / Ekspedisi</label>
                    <input type="text" name="courier_or_driver_name" id="courier_or_driver_name" placeholder="Contoh: Supardi (JNE Cargo / Truk)" class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- Tanggal Masuk -->
                <div>
                    <label for="transaction_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tanggal Penerimaan Fisik <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Penerimaan Gudang</label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Kondisi kardus, segel pabrik, atau no pallet..." class="w-full py-2 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

            </div>
        </div>

        <!-- DAFTAR ITEM MASUK -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Daftar Barang & Kuantitas Masuk</h2>
                    <p class="text-xs text-slate-400">Pilih produk, harga satuan beli, dan input serial number jika perangkat aktif</p>
                </div>

                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs border border-emerald-200 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Baris</span>
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Item #<span x-text="index + 1"></span></span>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-xs text-rose-600 hover:text-rose-800 font-semibold p-1">
                                &times; Hapus Baris
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            <div class="md:col-span-6">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Perangkat / Material</label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} [{{ $p->sku }}]</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Qty Masuk</label>
                                <input type="number" step="any" min="0.01" :name="'items[' + index + '][qty]'" x-model="item.qty" placeholder="1" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl font-mono font-bold min-h-[44px]">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Harga Beli Satuan (Rp)</label>
                                <input type="number" step="any" :name="'items[' + index + '][cost_price]'" x-model="item.cost_price" placeholder="0" class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl font-mono min-h-[44px]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Daftar Serial Number (Pisahkan dengan koma atau baris baru)</label>
                            <input type="text" :name="'items[' + index + '][serial_numbers]'" placeholder="Contoh: ZTEGCA881001, ZTEGCA881002, ZTEGCA881003..." class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg font-mono">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('stock-in.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold min-h-[44px]">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold shadow-md transition-all min-h-[44px]">
                Simpan & Update Saldo Stok
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function inboundForm() {
        return {
            items: [
                { product_id: '', qty: 1, cost_price: '' }
            ],
            addItem() {
                this.items.push({ product_id: '', qty: 1, cost_price: '' });
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
