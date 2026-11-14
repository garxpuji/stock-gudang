@extends('layouts.app')

@section('title', 'Loket Pengeluaran Barang Teknisi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="outboundForm()">

    <!-- HEADER -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Loket Pengeluaran Barang</h1>
            </div>
            <p class="text-sm text-slate-500 mt-1">Form serah terima fisik perangkat & material ke Teknisi / Karyawan Gayuh Media Informatika.</p>
        </div>
        <a href="{{ route('stock-out.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Riwayat Outbound
        </a>
    </div>

    <form method="POST" action="{{ route('stock-out.store') }}" class="space-y-6">
        @csrf

        <!-- KARTU 1: IDENTITAS PENERIMA FISIK (LOKET GUDANG) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    1
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Identitas Penerima Lapangan (Wajib)</h2>
                    <p class="text-xs text-slate-400">Pastikan orang yang mengambil secara fisik di loket gudang dicatat namanya</p>
                </div>
            </div>

            <!-- Tipe Penerima -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Jenis Penerima <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all" :class="recipientType === 'TEKNISI' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-bold' : 'border-slate-200 bg-white text-slate-600'">
                        <input type="radio" name="recipient_type" value="TEKNISI" x-model="recipientType" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block text-sm">Teknisi Lapangan</span>
                            <span class="block text-[11px] font-normal text-slate-500">PSB / Maintenance / Penarikan Kabel</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition-all" :class="recipientType === 'KARYAWAN' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-bold' : 'border-slate-200 bg-white text-slate-600'">
                        <input type="radio" name="recipient_type" value="KARYAWAN" x-model="recipientType" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block text-sm">Karyawan / Divisi Internal</span>
                            <span class="block text-[11px] font-normal text-slate-500">NOC / Server Datacenter / Kantor</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- picked_up_by -->
                <div>
                    <label for="picked_up_by" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap Pengambil Fisik <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="picked_up_by" id="picked_up_by" value="{{ old('picked_up_by') }}" placeholder="Contoh: Ahmad Wahyudi" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                    <div class="flex flex-wrap gap-1.5 mt-2">
                        <span class="text-[11px] text-slate-400">Teknisi sering:</span>
                        <button type="button" @click="document.getElementById('picked_up_by').value = 'Ahmad Wahyudi'" class="text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded">Ahmad Wahyudi</button>
                        <button type="button" @click="document.getElementById('picked_up_by').value = 'Rian Hidayat'" class="text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded">Rian Hidayat</button>
                        <button type="button" @click="document.getElementById('picked_up_by').value = 'Dani Ramadhan'" class="text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded">Dani Ramadhan</button>
                    </div>
                </div>

                <!-- Tim / Divisi -->
                <div>
                    <label for="recipient_department_or_team" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Divisi / Tim Lapangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="recipient_department_or_team" id="recipient_department_or_team" value="{{ old('recipient_department_or_team') }}" placeholder="Contoh: Tim Fiber 1 (PSB Barat)" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                </div>

                <!-- No Tiket / SPK / ID Pelanggan -->
                <div>
                    <label for="reference_ticket_no" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        No. SPK / Tiket Gangguan / ID Pelanggan
                    </label>
                    <input type="text" name="reference_ticket_no" id="reference_ticket_no" value="{{ old('reference_ticket_no') }}" placeholder="Contoh: SPK-PSB-202611-042 atau TKT-MTN-089" class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono min-h-[44px]">
                </div>

                <!-- No Telepon -->
                <div>
                    <label for="recipient_phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        No. Kontak / WhatsApp
                    </label>
                    <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" placeholder="08xxxxxxxxxx" class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                </div>

                <!-- Tanggal Pengeluaran -->
                <div>
                    <label for="transaction_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Waktu Pengeluaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date" value="{{ old('transaction_date', date('Y-m-d\TH:i')) }}" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                </div>

                <!-- Tujuan Pengeluaran -->
                <div class="md:col-span-2">
                    <label for="purpose" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tujuan Keperluan Pengeluaran <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="purpose" id="purpose" value="{{ old('purpose') }}" placeholder="Contoh: Pasang Baru (PSB) Pelanggan Perumahan Griya Indah Blok C" required class="w-full py-3 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 min-h-[44px]">
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button type="button" @click="document.getElementById('purpose').value = 'Pasang Baru (PSB) Pelanggan FTTH'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg">PSB Pelanggan</button>
                        <button type="button" @click="document.getElementById('purpose').value = 'Maintenance Gangguan Kabel Optik Putus'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg">Maintenance Gangguan</button>
                        <button type="button" @click="document.getElementById('purpose').value = 'Relokasi ONT & Penarikan Ulang Dropcore'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg">Relokasi Pelanggan</button>
                        <button type="button" @click="document.getElementById('purpose').value = 'Peminjaman Alat Ukur (Splicer / OTDR)'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg">Peminjaman Alat</button>
                        <button type="button" @click="document.getElementById('purpose').value = 'Kebutuhan Perangkat PoP / Switch Datacenter'" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1 rounded-lg">Kebutuhan NOC</button>
                    </div>
                </div>

                <!-- Keterangan Detail -->
                <div class="md:col-span-2">
                    <label for="keterangan_pengeluaran" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Catatan Tambahan Loket
                    </label>
                    <textarea name="keterangan_pengeluaran" id="keterangan_pengeluaran" rows="2" placeholder="Catatan redaman awal, kelengkapan adaptor, atau nomor roll kabel..." class="w-full py-2.5 px-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ old('keterangan_pengeluaran') }}</textarea>
                </div>
            </div>
        </div>

        <!-- KARTU 2: DAFTAR BARANG YANG DISERAHKAN -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Daftar Barang & Alokasi Serial Number</h2>
                        <p class="text-xs text-slate-400">Pilih perangkat yang diserahkan dan alokasikan Serial Number jika perangkat aktif</p>
                    </div>
                </div>

                <button type="button" @click="addItem()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-semibold text-xs border border-emerald-200 min-h-[44px]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Baris Barang</span>
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

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                            <!-- Select Product -->
                            <div class="md:col-span-8">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">Pilih Perangkat / Material</label>
                                <select :name="'items[' + index + '][product_id]'" x-model="item.product_id" @change="onProductChange(index)" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 min-h-[44px]">
                                    <option value="">-- Pilih Barang dari Gudang --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-stock="{{ (float)$p->stock }}" data-unit="{{ $p->unit->abbreviation }}" data-has-sn="{{ $p->has_serial_number ? '1' : '0' }}">
                                            {{ $p->name }} [Stok: {{ $p->formatted_stock }} &bull; {{ $p->location?->code ?? 'Gudang' }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Qty -->
                            <div class="md:col-span-4">
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Kuantitas Keluar (<span x-text="item.unit || 'Satuan'"></span>)
                                </label>
                                <input type="number" step="any" min="0.01" :max="item.max_stock" :name="'items[' + index + '][qty]'" x-model="item.qty" placeholder="1" required class="w-full py-2.5 px-3 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono font-bold min-h-[44px]">
                                <span class="block text-[11px] text-slate-400 mt-1" x-show="item.max_stock">
                                    Tersedia: <strong class="text-emerald-700" x-text="item.max_stock"></strong> <span x-text="item.unit"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Serial Numbers Selector (If Product Has SN) -->
                        <div x-show="item.has_sn" class="pt-2 border-t border-slate-200">
                            <label class="block text-xs font-bold text-slate-800 mb-1">
                                Pilih Serial Number Unit (Wajib Centang Sesuai Qty)
                            </label>
                            
                            <!-- Available serials list -->
                            <div class="max-h-40 overflow-y-auto bg-white p-3 rounded-xl border border-slate-200 space-y-1.5">
                                <template x-for="sn in getAvailableSerials(item.product_id)" :key="sn.id">
                                    <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer select-none text-xs">
                                        <input type="checkbox" :name="'items[' + index + '][serial_ids][]'" :value="sn.id" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                        <span class="font-mono font-bold text-slate-900 cursor-pointer" x-text="sn.serial_number"></span>
                                        <span class="text-slate-400 font-mono cursor-pointer" x-show="sn.mac_address" x-text="'(' + sn.mac_address + ')'"></span>
                                    </label>
                                </template>
                                <div x-show="getAvailableSerials(item.product_id).length === 0" class="text-xs text-amber-700 italic py-2">
                                    Belum ada serial number berstatus AVAILABLE di database untuk perangkat ini.
                                </div>
                            </div>
                        </div>

                        <!-- Notes Item -->
                        <div>
                            <input type="text" :name="'items[' + index + '][notes]'" placeholder="Catatan untuk item ini (opsional)" class="w-full py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- TOMBOL SIMPAN / PROSES -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('stock-out.index') }}" class="px-5 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-semibold min-h-[44px]">
                Batal
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-sm font-bold shadow-md transition-all min-h-[44px] flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Proses Serah Terima & Cetak Bukti</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    // Serials dataset serialized from backend
    const allProductsSerials = {
        @foreach($products as $p)
            @if($p->has_serial_number)
                "{{ $p->id }}": [
                    @foreach($p->availableSerials as $sn)
                        { id: {{ $sn->id }}, serial_number: "{{ $sn->serial_number }}", mac_address: "{{ $sn->mac_address ?? '' }}" },
                    @endforeach
                ],
            @endif
        @endforeach
    };

    function outboundForm() {
        return {
            recipientType: 'TEKNISI',
            items: [
                { product_id: '', qty: 1, unit: '', max_stock: 0, has_sn: false }
            ],
            addItem() {
                this.items.push({ product_id: '', qty: 1, unit: '', max_stock: 0, has_sn: false });
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
