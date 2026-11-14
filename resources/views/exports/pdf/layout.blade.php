<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') - Gayuh Media Informatika</title>
    <style>
        @page {
            margin: 12mm 10mm 15mm 10mm;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 8.5pt;
            line-height: 1.35;
        }
        .header-container {
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .company-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: -0.3px;
        }
        .company-sub {
            font-size: 8.5pt;
            color: #059669;
            font-weight: 600;
            margin-top: 2px;
        }
        .company-desc {
            font-size: 7.5pt;
            color: #64748b;
            margin-top: 1px;
        }
        .report-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            text-transform: uppercase;
        }
        .meta-text {
            font-size: 7.5pt;
            color: #64748b;
            text-align: right;
            margin-top: 3px;
        }
        .filters-banner {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 5px 8px;
            margin-bottom: 10px;
            font-size: 7.5pt;
            color: #475569;
        }
        .filters-banner strong {
            color: #0f172a;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th {
            background-color: #059669;
            color: #ffffff;
            font-weight: bold;
            font-size: 7.5pt;
            text-transform: uppercase;
            padding: 6px 5px;
            border: 1px solid #047857;
            text-align: left;
            vertical-align: middle;
        }
        table.data-table td {
            padding: 4.5px 5px;
            border: 1px solid #cbd5e1;
            font-size: 7.5pt;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
            font-size: 7pt;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .badge {
            display: inline-block;
            padding: 1.5px 4px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .badge-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-danger { background-color: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3; }
        .badge-info { background-color: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .badge-slate { background-color: #f1f5f9; color: #334155; border: 1px solid #e2e8f0; }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .summary-box {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 7.5pt;
        }
        .footer {
            position: fixed;
            bottom: -8mm;
            left: 0;
            right: 0;
            height: 6mm;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
            font-size: 7pt;
            color: #94a3b8;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>
<body>

    <!-- HEADER LAPORAN -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    <div class="company-title">PT GAYUH MEDIA INFORMATIKA</div>
                    <div class="company-sub">Sistem Logistik & Inventaris Gudang ISP</div>
                    <div class="company-desc">Pusat Distribusi Perangkat Aktif, Drop Core Fiber Optic & Operasional Teknisi</div>
                </td>
                <td style="width: 45%; vertical-align: top;">
                    <div class="report-title">@yield('report_title')</div>
                    <div class="meta-text">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
                    <div class="meta-text">Petugas: {{ Auth::user()->name ?? 'Admin Gudang' }}</div>
                </td>
            </tr>
        </table>
    </div>

    @yield('filters')

    <!-- KONTEN TABEL DATA -->
    @yield('content')

    <!-- FOOTER LAPORAN -->
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 70%; text-align: left;">
                    Dokumen inventaris resmi PT Gayuh Media Informatika &bull; Bersifat rahasia untuk operasional internal gudang.
                </td>
                <td style="width: 30%; text-align: right;">
                    Halaman <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 7;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size);
            $pdf->page_text($pdf->get_width() - 35 - $width, $pdf->get_height() - 25, $text, $font, $size, array(0.58, 0.64, 0.72));
        }
    </script>
</body>
</html>
