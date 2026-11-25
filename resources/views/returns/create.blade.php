@extends('layouts.app')

@section('title', 'Penerimaan Barang Kembali & QC')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="returnForm()">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Form Pengembalian Barang &amp; QC</h1>
            <p class="text-sm text-slate-600 mt-0.5">Terima barang kembali dari lapangan dan tentukan kondisinya (Baik atau Rusak).</p>
        </div>
        <a href="{{ route('returns.index') }}" class="inline-flex items-center px-3 py-2 min-h-[44px] text-sm font-semibold text-slate-700 hover:text-slate-900 rounded-lg hover:bg-slate-100">
            Riwayat Retur
        </a>
    </div>

    <form method="POST" action="{{ route('returns.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <!-- KARTU 1: IDENTITAS PENGEMBALI -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2.5">1. Data Pengembali</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="returned_by" class="block text-xs font-bold text-slate-700 mb-1">
                        Nama Pengembali <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="returned_by" id="returned_by" value="{{ old('returned_by') }}" placeholder="Nama teknisi atau staf" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <div>
                    <label for="return_category" class="block text-xs font-bold text-slate-700 mb-1">
                        Jenis Pengembalian <span class="text-rose-600">*</span>
                    </label>
                    <select name="return_category" id="return_category" required class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                        <option value="Sisa Material Proyek PSB">Sisa Material Proyek</option>
                        <option value="Bongkaran Pelanggan (Dismantle Cacat)">Bongkaran / Tarikan Pelanggan (Dismantle)</option>
                        <option value="Pengembalian Alat Kerja (Tools)">Pengembalian Alat Kerja</option>
                        <option value="Pengembalian Perangkat PoP / Kantor">Pengembalian Perangkat Kantor / PoP</option>
                    </select>
                </div>
            </div>

            <!-- DETAIL OPSIONAL (DILIPAT) -->
            <details class="pt-2 border-t border-slate-100 group">
                <summary class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 cursor-pointer py-2 select-none list-none flex items-center gap-1.5">
                    <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>Isi detail tambahan (No. Tiket/SPK, Kontak, Waktu, Alasan)</span>
                </summary>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3">
                    <div>
                        <label for="reference_ticket_no" class="block text-xs font-semibold text-slate-700 mb-1">No. Tiket / SPK</label>
                        <input type="text" name="reference_ticket_no" id="reference_ticket_no" value="{{ old('reference_ticket_no') }}" placeholder="Nomor tiket atau SPK" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono min-h-[44px]">
                    </div>
                    <div>
                        <label for="returned_by_contact" class="block text-xs font-semibold text-slate-700 mb-1">No. Telepon / Tim</label>
                        <input type="text" name="returned_by_contact" id="returned_by_contact" value="{{ old('returned_by_contact') }}" placeholder="Nomor kontak atau tim" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                    <div>
                        <label for="recipient_type" class="block text-xs font-semibold text-slate-700 mb-1">Pihak Pengembali</label>
                        <select name="recipient_type" id="recipient_type" required class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                            <option value="TEKNISI">Teknisi Lapangan</option>
                            <option value="KARYAWAN">Karyawan Internal</option>
                        </select>
                    </div>
                    <div>
                        <label for="transaction_date" class="block text-xs font-semibold text-slate-700 mb-1">Waktu Pengembalian</label>
                        <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d\TH:i')) }}" required class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="keterangan_pengembalian" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengembalian</label>
                        <input type="text" name="keterangan_pengembalian" id="keterangan_pengembalian" value="{{ old('keterangan_pengembalian') }}" placeholder="Alasan pengembalian barang" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl min-h-[44px]">
                    </div>
                </div>
            </details>
        </div>

        <!-- KARTU 2: BARANG & KONDISI QC -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 space-y-4">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">2. Daftar Barang &amp; Hasil Pemeriksaan (QC)</h2>
                <button type="button" @click="addItem()" class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-semibold text-xs border border-emerald-200 min-h-[44px] cursor-pointer">
                    <span>+ Tambah Barang</span>
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-4 rounded-xl border space-y-3" :class="item.qc_status === 'GOOD' ? 'border-slate-200 bg-slate-50/60' : 'border-rose-200 bg-rose-50/20'">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                            <div class="sm:col-span-8">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang <span class="text-rose-600">*</span></label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" @change="onProductChange(index)" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-has-sn="{{ $p->has_serial_number ? '1' : '0' }}">
                                        {{ $p->name }} [{{ $p->sku }}]
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah <span class="text-rose-600">*</span></label>
                                <input type="number" step="any" min="0.01" :name="'items[' + index + '][qty]'" x-model="item.qty" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl font-mono font-bold min-h-[44px]">
                            </div>

                            <div class="sm:col-span-1 flex sm:justify-end sm:pt-6">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="min-h-[44px] min-w-[44px] inline-flex items-center justify-center text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-semibold px-2 cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- PILIHAN KONDISI QC -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kondisi Barang <span class="text-rose-600">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-center gap-2.5 p-3 min-h-[44px] rounded-xl border cursor-pointer" :class="item.qc_status === 'GOOD' ? 'border-emerald-600 bg-white text-emerald-950 font-semibold' : 'border-slate-200 bg-white text-slate-700'">
                                    <input type="radio" :name="'items[' + index + '][qc_status]'" value="GOOD" x-model="item.qc_status" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="block text-sm">Baik / Normal</span>
                                        <span class="block text-[11px] font-normal text-slate-500">Masuk kembali ke stok aktif</span>
                                    </div>
                                </label>

                                <label class="flex items-center gap-2.5 p-3 min-h-[44px] rounded-xl border cursor-pointer" :class="item.qc_status === 'DAMAGED' ? 'border-rose-600 bg-white text-rose-950 font-semibold' : 'border-slate-200 bg-white text-slate-700'">
                                    <input type="radio" :name="'items[' + index + '][qc_status]'" value="DAMAGED" x-model="item.qc_status" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-sm">Rusak / Cacat</span>
                                        <span class="block text-[11px] font-normal text-slate-500">Masuk ke area karantina</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- JIKA RUSAK: KATEGORI & DETAIL KERUSAKAN -->
                        <div x-show="item.qc_status === 'DAMAGED'" class="p-3.5 rounded-xl bg-white border border-rose-200 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Penyebab Kerusakan</label>
                                <select :name="'items[' + index + '][damage_category]'" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-lg min-h-[40px]">
                                    <option value="Tersambar Petir / Overvoltage">Tersambar Petir / Overvoltage</option>
                                    <option value="Port LAN/Fiber Rusak">Port LAN/Fiber Rusak</option>
                                    <option value="Optic Laser Drop">Optic Laser Drop</option>
                                    <option value="Fisik Pecah / Cacat">Fisik Pecah / Cacat</option>
                                    <option value="Mati Total">Mati Total</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Bukti (Opsional)</label>
                                <input type="file" :name="'items[' + index + '][photo_proof]'" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-600 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-800">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Kerusakan</label>
                                <input type="text" :name="'items[' + index + '][damage_description]'" placeholder="Jelaskan bagian yang rusak" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-lg min-h-[40px]">
                            </div>
                        </div>

                        <!-- SERIAL NUMBER & CATATAN QC -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Serial Number (Jika ada)</label>
                                <input type="text" :name="'items[' + index + '][serial_number]'" placeholder="Nomor SN perangkat" class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg font-mono min-h-[40px]">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">MAC Address (Opsional)</label>
                                <input type="text" :name="'items[' + index + '][mac_address]'" placeholder="MAC Address" class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg font-mono min-h-[40px]">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan QC (Opsional)</label>
                                <input type="text" :name="'items[' + index + '][qc_notes]'" placeholder="Hasil cek fungsi / redaman" class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg min-h-[40px]">
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('returns.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-sm font-semibold min-h-[44px] inline-flex items-center">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold transition-colors min-h-[44px] inline-flex items-center cursor-pointer">
                Simpan Retur &amp; Update Stok
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function returnForm() {
        return {
            items: [{
                product_id: '',
                qty: 1,
                qc_status: 'GOOD',
                has_sn: false
            }],
            addItem() {
                this.items.push({
                    product_id: '',
                    qty: 1,
                    qc_status: 'GOOD',
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
                    this.items[index].has_sn = opt.getAttribute('data-has-sn') === '1';
                }
            }
        };
    }
</script>
@endpush
@endsection