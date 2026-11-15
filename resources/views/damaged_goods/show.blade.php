@extends('layouts.app')

@section('title', 'Karantina ' . $damagedGood->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- BREADCRUMB & HEADER -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('damaged-goods.index') }}" class="hover:text-emerald-600">Area Karantina</a>
                <span>/</span>
                <span class="font-mono text-orange-700 font-bold">{{ $damagedGood->code }}</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $damagedGood->product->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $damagedGood->action_taken->badge() }}">
                    {{ $damagedGood->action_taken->label() }}
                </span>
            </div>
        </div>

        <a href="{{ route('damaged-goods.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-900">
            &larr; Kembali ke Karantina
        </a>
    </div>

    <!-- DETAIL CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-6 md:p-8 space-y-6">
        
        <!-- Identitas Kerusakan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm">
            <div>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block">Kode Kerusakan:</span>
                <span class="font-mono font-bold text-base text-orange-700 block mt-0.5">{{ $damagedGood->code }}</span>

                <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block mt-3">Serial Number / MAC:</span>
                @if($damagedGood->serial_number)
                    <span class="font-mono font-bold text-slate-900 block mt-0.5">SN: {{ $damagedGood->serial_number }}</span>
                    @if($damagedGood->mac_address)
                        <span class="font-mono text-xs text-slate-500 block">MAC: {{ $damagedGood->mac_address }}</span>
                    @endif
                @else
                    <span class="text-slate-400 italic block mt-0.5">Non-Serial / Material Pasif</span>
                @endif
            </div>

            <div>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block">Kategori Kerusakan Teknis:</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border mt-1 {{ $damagedGood->damage_category->color() }}">
                    {{ $damagedGood->damage_category->value }}
                </span>

                <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block mt-3">Pelapor / Pengembali:</span>
                <span class="font-bold text-slate-900 block mt-0.5">{{ $damagedGood->reported_by }}</span>
                <span class="text-xs text-slate-500 block">Waktu: {{ $damagedGood->reported_at->format('d F Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- Deskripsi Kerusakan -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Keterangan Gejala Kerusakan Fisik / Optik</h3>
            <div class="p-4 rounded-xl bg-orange-50/40 border border-orange-200 text-sm text-orange-950 font-sans leading-relaxed">
                {{ $damagedGood->damage_description }}
            </div>
        </div>

        <!-- Photo Proof (if available) -->
        @if($damagedGood->photo_proof)
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Foto Bukti Fisik Kerusakan</h3>
                <div class="rounded-xl overflow-hidden border border-slate-200 max-w-md">
                    <img src="{{ asset('storage/' . $damagedGood->photo_proof) }}" alt="Bukti Kerusakan" class="w-full object-cover">
                </div>
            </div>
        @endif

        <!-- STATUS PENYELESAIAN JIKA SUDAH SELESAI -->
        @if($damagedGood->action_taken !== \App\Enums\DamagedActionTaken::PENDING)
            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Histori Tindakan Lanjutan</span>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $damagedGood->action_taken->badge() }}">
                        {{ $damagedGood->action_taken->label() }}
                    </span>
                    <span class="text-xs text-slate-500">Diproses pada: {{ $damagedGood->action_date?->format('d/m/Y H:i') }}</span>
                    @if($damagedGood->resolvedBy)
                        <span class="text-xs text-slate-500">&bull; Oleh: <strong class="text-slate-700">{{ $damagedGood->resolvedBy->name }}</strong></span>
                    @endif
                </div>

                @if($damagedGood->loss_cost)
                    <div class="text-sm font-bold text-rose-700 pt-1">
                        Nilai Kerugian Aset (Loss Cost): Rp {{ number_format($damagedGood->loss_cost, 0, ',', '.') }}
                    </div>
                @endif

                @if($damagedGood->action_notes)
                    <p class="text-xs text-slate-600 italic pt-1">"{{ $damagedGood->action_notes }}"</p>
                @endif
            </div>
        @endif

    </div>

    <!-- FORM TINDAKAN LANJUTAN JIKA MASIH PENDING -->
    @if($damagedGood->action_taken === \App\Enums\DamagedActionTaken::PENDING)
        <div class="bg-white rounded-2xl border-2 border-emerald-500/40 shadow-xs p-6 md:p-8 space-y-6" x-data="{ selectedAction: 'RMA_VENDOR' }">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-bold text-slate-900">Eksekusi Tindakan Lanjutan (Karantina)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pilih keputusan logistik: ajukan garansi ke vendor, hapus buku aset, atau perbaiki di laboratorium teknisi internal.</p>
            </div>

            <form method="POST" action="{{ route('damaged-goods.resolve', $damagedGood) }}" class="space-y-5">
                @csrf

                <!-- Pilihan 3 Tindakan -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- RMA Vendor -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between" :class="selectedAction === 'RMA_VENDOR' ? 'border-purple-600 bg-purple-50/50 text-purple-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-700'">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="radio" name="action_taken" value="RMA_VENDOR" x-model="selectedAction" class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                            <span class="text-sm">Klaim Garansi (RMA)</span>
                        </div>
                        <span class="text-xs font-normal text-slate-500">Kirim unit rusak ke vendor distributor untuk proses tukar unit / claim garansi.</span>
                    </label>

                    <!-- Pemusnahan Scrapped -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between" :class="selectedAction === 'SCRAPPED' ? 'border-rose-600 bg-rose-50/50 text-rose-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-700'">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="radio" name="action_taken" value="SCRAPPED" x-model="selectedAction" class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                            <span class="text-sm">Write-Off (Scrapped)</span>
                        </div>
                        <span class="text-xs font-normal text-slate-500">Barang dihapus permanen dari inventaris karena afkir total. Tercatat nilai kerugian aset.</span>
                    </label>

                    <!-- Perbaikan Internal -->
                    <label class="p-4 rounded-xl border-2 cursor-pointer transition-all flex flex-col justify-between" :class="selectedAction === 'REPAIRED_INTERNAL' ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-bold shadow-xs' : 'border-slate-200 bg-white text-slate-700'">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="radio" name="action_taken" value="REPAIRED_INTERNAL" x-model="selectedAction" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm">Perbaikan Internal</span>
                        </div>
                        <span class="text-xs font-normal text-slate-500">Berhasil diperbaiki tim teknisi internal. Unit dikembalikan ke stok aktif gudang.</span>
                    </label>
                </div>

                <!-- Input conditional per action -->
                <div x-show="selectedAction === 'RMA_VENDOR'" class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nomor Dokumen RMA / Resi Pengiriman Vendor</label>
                    <input type="text" name="rma_number" placeholder="Contoh: RMA-ZTE-2026-091" class="w-full py-2.5 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                </div>

                <div x-show="selectedAction === 'SCRAPPED'" class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Nilai Taksiran Kerugian Aset (Loss Cost)</label>
                    <input type="number" step="0.01" name="loss_cost" value="{{ ($damagedGood->product?->cost_price ?? 0) * $damagedGood->qty }}" placeholder="Rp" class="w-full py-2.5 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Tambahan Keputusan</label>
                    <textarea name="action_notes" rows="2" placeholder="Catatan persetujuan lead teknisi / berita acara pemusnahan..." class="w-full py-2 px-3 text-sm bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-semibold text-sm shadow-xs transition-colors">
                        Simpan & Eksekusi Keputusan
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
@endsection
