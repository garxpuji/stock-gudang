<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengeluaran Barang - {{ $stockOut->transaction_code }}</title>
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
        
        /* KOP SURAT */
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
            letter-spacing: 0.5px;
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

        /* METADATA GRID */
        .meta-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .meta-table td { padding: 4px 6px; font-size: 10pt; vertical-align: top; }
        .meta-label { width: 140px; color: #64748b; font-weight: bold; }
        .meta-sep { width: 15px; text-align: center; color: #94a3b8; }
        .meta-val { color: #0f172a; }

        /* ITEMS TABLE */
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

        /* TANDA TANGAN */
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

        .notes-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 10px 14px;
            font-size: 9pt;
            color: #475569;
            border-radius: 6px;
            margin-bottom: 20px;
        }

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
        <button onclick="window.print()" class="btn-print">&#128424; Cetak Dokumen</button>
    </div>

    <!-- KOP SURAT RESMI -->
    <div class="kop">
        <div class="kop-logo">G</div>
        <div class="kop-text">
            <h1>PT Gayuh Media Informatika</h1>
            <p>Internet Service Provider & Optical Telecommunication Network</p>
            <p>Jl. Jaringan Optik Utama No. 88, Gedung Cyber Network Lt. 2 &bull; Telp: (021) 5566-7788 &bull; Email: logistic@gayuhmedia.id</p>
        </div>
    </div>

    <div class="title">Surat Serah Terima / Bukti Pengeluaran Barang</div>

    <!-- METADATA TRANSAKSI -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">No. Dokumen</td>
            <td class="meta-sep">:</td>
            <td class="meta-val font-mono" style="font-weight: bold; color: #047857;">{{ $stockOut->transaction_code }}</td>
            <td class="meta-label">Tanggal / Waktu</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $stockOut->transaction_date->format('d/m/Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Nama Pengambil Fisik</td>
            <td class="meta-sep">:</td>
            <td class="meta-val" style="font-weight: bold;">{{ $stockOut->picked_up_by }}</td>
            <td class="meta-label">Pihak Penerima</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $stockOut->recipient_type->label() }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tim Lapangan / Divisi</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $stockOut->recipient_department_or_team }}</td>
            <td class="meta-label">No. SPK / Tiket / ID</td>
            <td class="meta-sep">:</td>
            <td class="meta-val font-mono" style="font-weight: bold;">{{ $stockOut->reference_ticket_no ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Keperluan Pengeluaran</td>
            <td class="meta-sep">:</td>
            <td class="meta-val" colspan="4"><strong>{{ $stockOut->purpose }}</strong></td>
        </tr>
    </table>

    <!-- DAFTAR BARANG -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Nama Perangkat / Material</th>
                <th style="width: 120px;">Kode SKU</th>
                <th style="width: 70px;" class="text-center">Jumlah</th>
                <th>Serial Number / MAC Address (Perangkat Aktif)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($stockOut->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        <div style="font-size: 8.5pt; color: #64748b;">{{ $item->product->category->name }}</div>
                    </td>
                    <td class="font-mono" style="font-size: 9pt;">{{ $item->product->sku }}</td>
                    <td class="text-center font-mono" style="font-weight: bold;">
                        {{ number_format($item->qty, 0) }} {{ $item->product->unit->abbreviation }}
                    </td>
                    <td>
                        @if($item->serials->count() > 0)
                            <div style="font-size: 9pt; font-family: monospace;">
                                @foreach($item->serials as $sn)
                                    <div>&bull; <strong>{{ $sn->serial_number }}</strong> @if($sn->mac_address) <span style="color:#64748b;">(MAC: {{ $sn->mac_address }})</span> @endif</div>
                                @endforeach
                            </div>
                        @else
                            <span style="color: #94a3b8; font-style: italic;">Material Habis Pakai (Non-SN)</span>
                        @endif
                        @if($item->notes)
                            <div style="font-size: 8.5pt; color: #64748b; margin-top: 3px;">Catatan: {{ $item->notes }}</div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($stockOut->keterangan_pengeluaran)
        <div class="notes-box">
            <strong>Catatan Tambahan Loket:</strong> {{ $stockOut->keterangan_pengeluaran }}
        </div>
    @endif

    <!-- TANDA TANGAN RESMI -->
    <div class="signatures">
        <div class="sign-box">
            <div>Yang Menyerahkan,</div>
            <div style="font-size: 8.5pt; color: #64748b;">Admin Gudang Logistik</div>
            <div class="sign-space"></div>
            <div class="sign-name">{{ $stockOut->dispatchedBy->name }}</div>
            <div class="sign-role">Petugas Gudang GMI</div>
        </div>

        <div class="sign-box">
            <div>Yang Mengambil Fisik,</div>
            <div style="font-size: 8.5pt; color: #64748b;">Teknisi / Karyawan Lapangan</div>
            <div class="sign-space"></div>
            <div class="sign-name">{{ $stockOut->picked_up_by }}</div>
            <div class="sign-role">{{ $stockOut->recipient_department_or_team }}</div>
        </div>

        <div class="sign-box">
            <div>Mengetahui,</div>
            <div style="font-size: 8.5pt; color: #64748b;">Lead Field Operations / NOC</div>
            <div class="sign-space"></div>
            <div class="sign-name">Tegar Pratama</div>
            <div class="sign-role">Principal NOC & Ops</div>
        </div>
    </div>

    <div style="margin-top: 35px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8pt; color: #94a3b8; text-align: center;">
        Dokumen ini diterbitkan secara otomatis oleh Sistem Informasi Inventaris & Logistik Gudang PT Gayuh Media Informatika &bull; Dicetak pada: {{ date('d/m/Y H:i:s') }}
    </div>

</div>

</body>
</html>
