@extends('layouts.app')

@section('title', 'Penerimaan Barang Kembali & QC Inspection')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="returnForm()">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Form Pengembalian Barang & QC Inspection</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Inspeksi fisik dan pemilahan: Barang Baik (Restock) vs Barang Cacat/Dismantle (Area Karantina).</p>
        </div>
        <a href="{{ route('returns.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Riwayat Retur
        </a>
    </div>

    <form method="POST" action="{{ route('returns.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- KARTU 1: IDENTITAS PENGEMBALI -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Identitas Pihak Pengembali</h2>
                    <p class="text-xs text-slate-400">Teknisi atau karyawan yang membawa fisik barang kembali ke gudang</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- returned_by -->
                <div>
                    <label for="returned_by" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap Pengembali <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="returned_by" id="returned_by" value="{{ old('returned_by') }}" placeholder="Contoh: Ahmad Wahyudi atau Rian Hidayat" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- returned_by_contact -->
                <div>
                    <label for="returned_by_contact" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        No. Telepon / Tim Lapangan
                    </label>
                    <input type="text" name="returned_by_contact" id="returned_by_contact" value="{{ old('returned_by_contact') }}" placeholder="Contoh: 0812345678 (Tim Fiber 1)" class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- Kategori Pengembalian -->
                <div>
                    <label for="return_category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Pengembalian <span class="text-rose-500">*</span>
                    </label>
                    <select name="return_category" id="return_category" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                        <option value="Sisa Material Proyek PSB">Sisa Material Proyek (Kabel Dropcore, Fast Connector)</option>
                        <option value="Bongkaran Pelanggan (Dismantle Cacat)">Bongkaran Pelanggan (Dismantle / Churn / Ganti Alat)</option>
                        <option value="Pengembalian Alat Kerja (Tools)">Pengembalian Alat Kerja (Splicer, OTDR, OPM)</option>
                        <option value="Pengembalian Perangkat PoP / Kantor">Pengembalian Perangkat PoP / Kantor</option>
                    </select>
                </div>

                <!-- No Tiket / SPK Asal -->
                <div>
                    <label for="reference_ticket_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        No. Tiket Gangguan / SPK / ID Pelanggan Asal
                    </label>
                    <input type="text" name="reference_ticket_no" id="reference_ticket_no" value="{{ old('reference_ticket_no') }}" placeholder="Contoh: TKT-MTN-202611-109 atau SPK-PSB-042" class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 font-mono min-h-[44px]">
                </div>

                <!-- Tanggal Pengembalian -->
                <div>
                    <label for="transaction_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Waktu Pengembalian <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d\TH:i')) }}" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                </div>

                <!-- Jenis Penerima -->
                <div>
                    <label for="recipient_type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Pihak Pengembali <span class="text-rose-500">*</span>
                    </label>
                    <select name="recipient_type" id="recipient_type" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                        <option value="TEKNISI">Teknisi Lapangan (PSB / Maintenance)</option>
                        <option value="KARYAWAN">Karyawan / Divisi Internal</option>
                    </select>
                </div>

                <!-- Keterangan Alasan Pengembalian -->
                <div class="md:col-span-2">
                    <label for="keterangan_pengembalian" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alasan / Detail Pengembalian
                    </label>
                    <textarea name="keterangan_pengembalian" id="keterangan_pengembalian" rows="2" placeholder="Contoh: Modem tarikan pelanggan churn berhenti langganan karena pindah rumah, sisa kabel tarikan PSB..." class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500">{{ old('keterangan_pengembalian') }}</textarea>
                </div>

            </div>
        </div>

        <!-- KARTU 2: QC INSPECTION PER BARANG -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Inspeksi Quality Control (QC Gudang)</h2>
                        <p class="text-xs text-slate-400">Pemeriksaan kondisi fisik, redaman optik, atau port sebelum dimasukkan ke sistem</p>
                    </div>
                </div>

                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs border border-emerald-200 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Barang Retur</span>
                </button>
            </div>

            <div class="space-y-6">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-5 rounded-2xl border-2 space-y-4 transition-all" :class="item.qc_status === 'GOOD' ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20'">
                        
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Item Retur #<span x-text="index + 1"></span></span>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-xs text-rose-600 hover:text-rose-800 font-semibold p-1">
                                &times; Hapus Baris
                            </button>
                        </div>

                        <!-- Pilih Produk & Qty -->
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                            <div class="md:col-span-8">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Nama Perangkat / Material</label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" @change="onProductChange(index)" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                                    <option value="">-- Pilih Barang yang Dikembalikan --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-has-sn="{{ $p->has_serial_number ? '1' : '0' }}">
                                            {{ $p->name }} [{{ $p->sku }} &bull; {{ $p->category?->name ?? 'Gudang' }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kuantitas Kembali</label>
                                <input type="number" step="any" min="0.01" :name="'items[' + index + '][qty]'" x-model="item.qty" placeholder="1" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono font-bold min-h-[44px]">
                            </div>
                        </div>

                        <!-- HASIL INSPEKSI QC (RADIO TOGGLE) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">
                                Hasil Pemeriksaan Kondisi Fisik & Fungsi (QC) <span class="text-rose-500">*</span>
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Opsi Baik -->
                                <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all" :class="item.qc_status === 'GOOD' ? 'border-emerald-600 bg-white text-emerald-950 font-bold shadow-xs' : 'border-slate-200 bg-white/70 text-slate-600'">
                                    <input type="radio" :name="'items[' + index + '][qc_status]'" value="GOOD" x-model="item.qc_status" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="block text-sm">Kondisi BAIK / LAYAK PAKAI</span>
                                        <span class="block text-[11px] font-normal text-emerald-700">Langsung dikembalikan ke stok aktif gudang (RESTOCK)</span>
                                    </div>
                                </label>

                                <!-- Opsi Rusak -->
                                <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all" :class="item.qc_status === 'DAMAGED' ? 'border-rose-600 bg-white text-rose-950 font-bold shadow-xs' : 'border-slate-200 bg-white/70 text-slate-600'">
                                    <input type="radio" :name="'items[' + index + '][qc_status]'" value="DAMAGED" x-model="item.qc_status" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-sm">Kondisi RUSAK / CACAT</span>
                                        <span class="block text-[11px] font-normal text-rose-700">Tidak menambah stok aktif, dialihkan ke KARANTINA RUSAK</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- INPUT SERIAL NUMBER JIKA ADA -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Serial Number Fisik Unit (SN)</label>
                                <input type="text" :name="'items[' + index + '][serial_number]'" placeholder="Contoh: ZTEGCA881008" class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">MAC Address (Opsional)</label>
                                <input type="text" :name="'items[' + index + '][mac_address]'" placeholder="Contoh: 48:D2:24:AB:CD:EF" class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono">
                            </div>
                        </div>

                        <!-- JIKA STATUS RUSAK: INPUT KATEGORI KERUSAKAN & DESKRIPSI -->
                        <div x-show="item.qc_status === 'DAMAGED'" x-transition class="p-4 rounded-xl bg-white border border-rose-200 space-y-3">
                            <div class="flex items-center gap-2 text-rose-700 font-bold text-xs uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <span>Klasifikasi Kerusakan Hardware</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Kategori Kerusakan</label>
                                    <select :name="'items[' + index + '][damage_category]'" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-lg">
                                        <option value="Tersambar Petir / Overvoltage">Tersambar Petir / Overvoltage</option>
                                        <option value="Port LAN/Fiber Rusak">Port LAN/Fiber Rusak</option>
                                        <option value="Optic Laser Drop">Optic Laser Drop</option>
                                        <option value="Fisik Pecah / Cacat">Fisik Pecah / Cacat</option>
                                        <option value="Mati Total">Mati Total</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Foto Bukti Fisik (Opsional)</label>
                                    <input type="file" name="photo_proof" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Detail Kerusakan Teknis</label>
                                <input type="text" :name="'items[' + index + '][damage_description]'" placeholder="Contoh: Port LAN 1 terbakar gosong pasca petir, LOS merah menyala terus" class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-lg">
                            </div>
                        </div>

                        <!-- Catatan QC Item -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Catatan Uji Redaman / Kondisi Fisik</label>
                            <input type="text" :name="'items[' + index + '][qc_notes]'" placeholder="Contoh: Redaman diukur -18.5dBm normal, casing utuh atau port terbakar..." class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg">
                        </div>

                    </div>
                </template>
            </div>
        </div>

        <!-- TOMBOL SIMPAN -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('returns.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold min-h-[44px]">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold shadow-md transition-all min-h-[44px] flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Simpan Penerimaan Retur & Update Stok</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function returnForm() {
        return {
            items: [
                { product_id: '', qty: 1, qc_status: 'GOOD', has_sn: false }
            ],
            addItem() {
                this.items.push({ product_id: '', qty: 1, qc_status: 'GOOD', has_sn: false });
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
