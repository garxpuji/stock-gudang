<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Penerimaan Barang Retur - {{ $return->transaction_code }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #1e293b;
            margin: 0;
            padding: 20px;
        }
        .container { max-width: 800px; margin: 0 auto; }
        
        .kop {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #047857;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .kop-logo {
            width: 60px;
            height: 60px;
            background: #059669;
            color: white;
            font-weight: bold;
            font-size: 24pt;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 15px;
        }
        .kop-text h1 {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .kop-text p {
            font-size: 9pt;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        .title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0 20px 0;
            color: #047857;
            letter-spacing: 0.5px;
        }

        .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .meta-table td { padding: 4px 6px; font-size: 10pt; vertical-align: top; }
        .meta-label { width: 150px; color: #64748b; font-weight: bold; }
        .meta-sep { width: 15px; text-align: center; color: #94a3b8; }
        .meta-val { color: #0f172a; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .items-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 10pt;
            vertical-align: top;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        .badge-good {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8.5pt;
            display: inline-block;
        }
        .badge-damaged {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 8.5pt;
            display: inline-block;
        }

        .signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-box {
            width: 30%;
            text-align: center;
            font-size: 10pt;
        }
        .sign-space { height: 75px; }
        .sign-name { font-weight: bold; border-top: 1px solid #0f172a; padding-top: 5px; }
        .sign-role { font-size: 8.5pt; color: #64748b; }

        .btn-print {
            background: #059669;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 20px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" class="btn-print">&#128424; Cetak Bukti Retur</button>
    </div>

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-logo">G</div>
        <div class="kop-text">
            <h1>PT Gayuh Media Informatika</h1>
            <p>Quality Control & Optical Warehouse Receiving Division</p>
            <p>Jl. Jaringan Optik Utama No. 88, Gedung Cyber Network Lt. 2 &bull; Telp: (021) 5566-7788</p>
        </div>
    </div>

    <div class="title">Berita Acara / Bukti Penerimaan Barang Kembali (Retur)</div>

    <!-- METADATA RETUR -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">No. Dokumen Retur</td>
            <td class="meta-sep">:</td>
            <td class="meta-val font-mono" style="font-weight: bold; color: #047857;">{{ $return->transaction_code }}</td>
            <td class="meta-label">Waktu Penerimaan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $return->transaction_date->format('d/m/Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Nama Pengembali</td>
            <td class="meta-sep">:</td>
            <td class="meta-val" style="font-weight: bold;">{{ $return->returned_by }}</td>
            <td class="meta-label">Kontak / Tim</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $return->returned_by_contact ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kategori Pengembalian</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $return->return_category }}</strong></td>
            <td class="meta-label">Ref. Tiket / SPK Asal</td>
            <td class="meta-sep">:</td>
            <td class="meta-val font-mono" style="font-weight: bold;">{{ $return->reference_ticket_no ?? '-' }}</td>
        </tr>
        @if($return->keterangan_pengembalian)
            <tr>
                <td class="meta-label">Alasan Pengembalian</td>
                <td class="meta-sep">:</td>
                <td class="meta-val" colspan="4"><em>{{ $return->keterangan_pengembalian }}</em></td>
            </tr>
        @endif
    </table>

    <!-- TABEL HASIL INSPEKSI QC -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Nama Perangkat / Material</th>
                <th style="width: 70px;" class="text-center">Jumlah</th>
                <th style="width: 140px;" class="text-center">Hasil Inspeksi QC</th>
                <th>Serial Number / MAC</th>
                <th>Tindakan Sistem & Catatan Fisik</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        <div style="font-size: 8.5pt; color: #64748b;" class="font-mono">{{ $item->product->sku }}</div>
                    </td>
                    <td class="text-center font-mono" style="font-weight: bold;">
                        {{ number_format($item->qty, 0) }} {{ $item->product->unit->abbreviation }}
                    </td>
                    <td class="text-center">
                        @if($item->qc_status === \App\Enums\QcStatus::GOOD)
                            <span class="badge-good">&#10003; BAIK / LAYAK</span>
                        @else
                            <span class="badge-damaged">&#10007; RUSAK / CACAT</span>
                        @endif
                    </td>
                    <td class="font-mono" style="font-size: 9pt;">
                        @if($item->serial_number)
                            <strong>{{ $item->serial_number }}</strong>
                            @if($item->mac_address)
                                <div style="color: #64748b; font-size: 8pt;">MAC: {{ $item->mac_address }}</div>
                            @endif
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="font-size: 9pt;">
                        @if($item->qc_status === \App\Enums\QcStatus::GOOD)
                            <strong style="color: #065f46;">Restocked ke Stok Aktif Gudang</strong>
                        @else
                            <strong style="color: #991b1b;">Dialihkan ke Karantina Rusak (Pending)</strong>
                        @endif
                        <div style="color: #64748b; font-style: italic; margin-top: 2px;">{{ $item->qc_notes }}</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="signatures">
        <div class="sign-box">
            <div>Yang Menyerahkan / Mengembalikan,</div>
            <div style="font-size: 8.5pt; color: #64748b;">Teknisi / Karyawan</div>
            <div class="sign-space"></div>
            <div class="sign-name">{{ $return->returned_by }}</div>
            <div class="sign-role">Pihak Pengembali</div>
        </div>

        <div class="sign-box">
            <div>Petugas QC & Penerima,</div>
            <div style="font-size: 8.5pt; color: #64748b;">Admin Gudang Logistik</div>
            <div class="sign-space"></div>
            <div class="sign-name">{{ $return->receivedBy->name }}</div>
            <div class="sign-role">Pemeriksa QC Gudang</div>
        </div>

        <div class="sign-box">
            <div>Mengetahui,</div>
            <div style="font-size: 8.5pt; color: #64748b;">Supervisor Operasional</div>
            <div class="sign-space"></div>
            <div class="sign-name">Budi Santoso</div>
            <div class="sign-role">Head of Logistics & Ops</div>
        </div>
    </div>

    <div style="margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8pt; color: #94a3b8; text-align: center;">
        Dokumen ini sah sebagai tanda terima pengembalian aset inventaris PT Gayuh Media Informatika &bull; Dicetak: {{ date('d/m/Y H:i:s') }}
    </div>

</div>

</body>
</html>
