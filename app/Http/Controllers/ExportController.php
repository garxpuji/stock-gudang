<?php

namespace App\Http\Controllers;

use App\Enums\DamagedActionTaken;
use App\Models\Category;
use App\Models\DamagedGood;
use App\Models\Location;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\StockOutTransaction;
use App\Support\SqlLike;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    private const MAX_EXPORT_ROWS = 5000;

    /**
     * Export Stok Tersedia (Active Product Catalog) ke Excel (.xlsx) atau PDF (.pdf)
     */
    public function exportProducts(Request $request): Response
    {
        $query = Product::with(['category', 'unit', 'location', 'availableSerials']);
        $activeFilters = [];

        if ($request->filled('search')) {
            $query->search($request->input('search'));
            $activeFilters[] = 'Pencarian: "'.$request->input('search').'"';
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
            $cat = Category::find($request->input('category_id'));
            if ($cat) {
                $activeFilters[] = 'Kategori: '.$cat->name;
            }
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->input('location_id'));
            $loc = Location::find($request->input('location_id'));
            if ($loc) {
                $activeFilters[] = 'Lokasi: '.$loc->name;
            }
        }

        if ($request->filled('status')) {
            match ($request->input('status')) {
                'LOW' => [$query->lowStock(), $activeFilters[] = 'Status: Stok Menipis'],
                'EMPTY' => [$query->outOfStock(), $activeFilters[] = 'Status: Stok Habis'],
                'SAFE' => [$query->whereColumn('stock', '>', 'min_stock'), $activeFilters[] = 'Status: Stok Aman'],
                'DAMAGED' => [$query->hasDamaged(), $activeFilters[] = 'Status: Ada Stok Rusak'],
                default => null,
            };
        }

        $products = $query->orderBy('name')->limit(self::MAX_EXPORT_ROWS)->get();
        $type = strtolower($request->input('type', 'excel'));

        Log::info('security.report_exported', [
            'report' => 'products',
            'format' => $type,
            'rows' => $products->count(),
            'user_id' => Auth::id(),
        ]);

        // 1. Ekspor ke PDF
        if ($type === 'pdf') {
            $filename = 'Laporan_Stok_Tersedia_'.now()->format('Y-m-d_His').'.pdf';
            $pdf = Pdf::loadView('exports.pdf.products', compact('products', 'activeFilters'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }

        // 2. Ekspor ke Excel (.xlsx)
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stok Tersedia');

        $headers = [
            'No',
            'SKU',
            'Barcode',
            'Nama Perangkat / Material',
            'Kategori',
            'Satuan',
            'Lokasi Rak / Bin',
            'Stok Fisik Tersedia',
            'Stok Rusak/Karantina',
            'Batas Min. Stok',
            'Status Stok',
            'Wajib Catat SN',
            'Daftar Serial Number Tersedia',
            'Harga Beli Satuan (Rp)',
            'Total Nilai Aset (Rp)',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($products as $p) {
            $serials = $p->availableSerials->pluck('serial_number')->implode(', ');
            $totalVal = (float) $p->stock * (float) $p->cost_price;

            $dataRows[] = [
                $no++,
                $p->sku,
                $p->barcode ?? '-',
                $p->name,
                $p->category?->name ?? '-',
                $p->unit?->name ?? '-',
                $p->location?->name ?? 'Gudang Utama',
                (float) $p->stock,
                (float) $p->damaged_stock,
                (float) $p->min_stock,
                $p->stock_status->label(),
                $p->has_serial_number ? 'Ya' : 'Tidak',
                $serials ?: '-',
                (float) $p->cost_price,
                $totalVal,
            ];
        }

        $this->buildExcelSheet($sheet, 'LAPORAN STOK TERSEDIA & KATALOG GUDANG', $headers, $dataRows, [
            8 => '#,##0',
            9 => '#,##0',
            10 => '#,##0',
            14 => '#,##0',
            15 => '#,##0',
        ]);

        $filename = 'Laporan_Stok_Tersedia_'.now()->format('Y-m-d_His').'.xlsx';

        return $this->downloadExcelResponse($spreadsheet, $filename);
    }

    /**
     * Export Barang Keluar / Pengambilan Teknisi ke Excel (.xlsx) atau PDF (.pdf)
     */
    public function exportStockOut(Request $request): Response
    {
        $query = StockOutTransaction::with(['dispatchedBy', 'items.product.unit', 'items.serials']);
        $activeFilters = [];

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $like = SqlLike::contains($search);
            $activeFilters[] = 'Pencarian: "'.$search.'"';
            $query->where(function ($q) use ($like) {
                $q->where('transaction_code', 'like', $like)
                    ->orWhere('picked_up_by', 'like', $like)
                    ->orWhere('reference_ticket_no', 'like', $like)
                    ->orWhere('recipient_department_or_team', 'like', $like)
                    ->orWhere('purpose', 'like', $like);
            });
        }

        if ($request->filled('technician')) {
            $query->where('picked_up_by', $request->input('technician'));
            $activeFilters[] = 'Teknisi: '.$request->input('technician');
        }

        if ($request->filled('recipient_type')) {
            $query->where('recipient_type', $request->input('recipient_type'));
            $activeFilters[] = 'Tipe Penerima: '.$request->input('recipient_type');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
            $activeFilters[] = 'Dari: '.$request->input('date_from');
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
            $activeFilters[] = 'Sampai: '.$request->input('date_to');
        }

        $transactions = $query->latest('transaction_date')->latest('id')->limit(self::MAX_EXPORT_ROWS)->get();
        $type = strtolower($request->input('type', 'excel'));

        Log::info('security.report_exported', [
            'report' => 'stock_out',
            'format' => $type,
            'rows' => $transactions->count(),
            'user_id' => Auth::id(),
        ]);

        // 1. Ekspor ke PDF
        if ($type === 'pdf') {
            $filename = 'Laporan_Barang_Keluar_'.now()->format('Y-m-d_His').'.pdf';
            $pdf = Pdf::loadView('exports.pdf.stock-out', compact('transactions', 'activeFilters'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }

        // 2. Ekspor ke Excel (.xlsx)
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Barang Keluar');

        $headers = [
            'No',
            'No. Transaksi',
            'Tanggal & Waktu',
            'Nama Pengambil (Teknisi / Karyawan)',
            'Tipe Penerima',
            'Divisi / Tim',
            'No. Telepon',
            'No. Tiket / SPK',
            'Tujuan Pemakaian',
            'SKU Barang',
            'Nama Barang',
            'Qty Keluar',
            'Satuan',
            'Serial Numbers',
            'Petugas Loket Gudang',
            'Keterangan Transaksi',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($transactions as $tx) {
            foreach ($tx->items as $item) {
                $sns = $item->serials->pluck('serial_number')->implode(', ');
                if (empty($sns) && ! empty($item->notes) && str_contains($item->notes, 'SN:')) {
                    $sns = $item->notes;
                }

                $dataRows[] = [
                    $no++,
                    $tx->transaction_code,
                    $tx->transaction_date->format('Y-m-d H:i:s'),
                    $tx->picked_up_by,
                    $tx->recipient_type->value,
                    $tx->recipient_department_or_team ?? '-',
                    $tx->recipient_phone ?? '-',
                    $tx->reference_ticket_no ?? '-',
                    $tx->purpose ?? '-',
                    $item->product?->sku ?? '-',
                    $item->product?->name ?? '-',
                    (float) $item->qty,
                    $item->product?->unit?->abbreviation ?? '-',
                    $sns ?: '-',
                    $tx->dispatchedBy?->name ?? 'Admin Gudang',
                    $tx->keterangan_pengeluaran ?? '-',
                ];
            }
        }

        $this->buildExcelSheet($sheet, 'LAPORAN PENGELUARAN BARANG KE TEKNISI', $headers, $dataRows, [
            12 => '#,##0',
        ]);

        $filename = 'Laporan_Barang_Keluar_'.now()->format('Y-m-d_His').'.xlsx';

        return $this->downloadExcelResponse($spreadsheet, $filename);
    }

    /**
     * Export Barang Kembali / Retur & QC ke Excel (.xlsx) atau PDF (.pdf)
     */
    public function exportReturns(Request $request): Response
    {
        $query = ReturnTransaction::with(['receivedBy', 'items.product.unit']);
        $activeFilters = [];

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $like = SqlLike::contains($search);
            $activeFilters[] = 'Pencarian: "'.$search.'"';
            $query->where(function ($q) use ($like) {
                $q->where('transaction_code', 'like', $like)
                    ->orWhere('returned_by', 'like', $like)
                    ->orWhere('reference_ticket_no', 'like', $like)
                    ->orWhere('return_category', 'like', $like);
            });
        }

        if ($request->filled('technician')) {
            $query->where('returned_by', $request->input('technician'));
            $activeFilters[] = 'Teknisi: '.$request->input('technician');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->input('date_from'));
            $activeFilters[] = 'Dari: '.$request->input('date_from');
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->input('date_to'));
            $activeFilters[] = 'Sampai: '.$request->input('date_to');
        }

        if ($request->filled('qc_status')) {
            $query->whereHas('items', function ($iq) use ($request) {
                $iq->where('qc_status', $request->input('qc_status'));
            });
            $activeFilters[] = 'Status QC: '.$request->input('qc_status');
        }

        $returns = $query->latest('transaction_date')->latest('id')->limit(self::MAX_EXPORT_ROWS)->get();
        $type = strtolower($request->input('type', 'excel'));

        Log::info('security.report_exported', [
            'report' => 'returns',
            'format' => $type,
            'rows' => $returns->count(),
            'user_id' => Auth::id(),
        ]);

        // 1. Ekspor ke PDF
        if ($type === 'pdf') {
            $filename = 'Laporan_Barang_Kembali_'.now()->format('Y-m-d_His').'.pdf';
            $pdf = Pdf::loadView('exports.pdf.returns', compact('returns', 'activeFilters'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }

        // 2. Ekspor ke Excel (.xlsx)
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Barang Kembali Retur');

        $headers = [
            'No',
            'No. Transaksi Retur',
            'Tanggal & Waktu',
            'Teknisi / Pengembali',
            'Kontak Pengembali',
            'Kategori Pengembalian',
            'Ref. No. Tiket / SPK',
            'SKU Barang',
            'Nama Barang',
            'Qty Kembali',
            'Satuan',
            'Hasil Inspeksi QC',
            'Tindakan Setelah QC',
            'Serial Number',
            'MAC Address',
            'Catatan QC',
            'Petugas QC / Penerima',
            'Keterangan Tambahan',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($returns as $ret) {
            foreach ($ret->items as $item) {
                $dataRows[] = [
                    $no++,
                    $ret->transaction_code,
                    $ret->transaction_date->format('Y-m-d H:i:s'),
                    $ret->returned_by,
                    $ret->returned_by_contact ?? '-',
                    $ret->return_category ?? '-',
                    $ret->reference_ticket_no ?? '-',
                    $item->product?->sku ?? '-',
                    $item->product?->name ?? '-',
                    (float) $item->qty,
                    $item->product?->unit?->abbreviation ?? '-',
                    $item->qc_status?->label() ?? '-',
                    $item->action_taken ?? '-',
                    $item->serial_number ?: '-',
                    $item->mac_address ?: '-',
                    $item->qc_notes ?? '-',
                    $ret->receivedBy?->name ?? 'Admin QC',
                    $ret->keterangan_pengembalian ?? '-',
                ];
            }
        }

        $this->buildExcelSheet($sheet, 'LAPORAN PENERIMAAN BARANG KEMBALI & RETUR QC', $headers, $dataRows, [
            10 => '#,##0',
        ]);

        $filename = 'Laporan_Barang_Kembali_'.now()->format('Y-m-d_His').'.xlsx';

        return $this->downloadExcelResponse($spreadsheet, $filename);
    }

    /**
     * Export Barang Rusak / Karantina ke Excel (.xlsx) atau PDF (.pdf)
     */
    public function exportDamagedGoods(Request $request): Response
    {
        $query = DamagedGood::with(['product.unit', 'resolvedBy']);
        $activeFilters = [];

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $like = SqlLike::contains($search);
            $activeFilters[] = 'Pencarian: "'.$search.'"';
            $query->where(function ($q) use ($like) {
                $q->where('code', 'like', $like)
                    ->orWhere('serial_number', 'like', $like)
                    ->orWhere('reported_by', 'like', $like)
                    ->orWhere('damage_description', 'like', $like)
                    ->orWhereHas('product', function ($pq) use ($like) {
                        $pq->where('name', 'like', $like)
                            ->orWhere('sku', 'like', $like);
                    });
            });
        }

        if ($request->filled('technician')) {
            $query->where('reported_by', $request->input('technician'));
            $activeFilters[] = 'Teknisi: '.$request->input('technician');
        }

        if ($request->filled('action_taken')) {
            if ($request->input('action_taken') === 'PENDING') {
                $query->where('action_taken', DamagedActionTaken::PENDING);
                $activeFilters[] = 'Status: Menunggu Keputusan (Karantina)';
            } elseif ($request->input('action_taken') === 'RESOLVED') {
                $query->where('action_taken', '!=', DamagedActionTaken::PENDING);
                $activeFilters[] = 'Status: Selesai Ditindaklanjuti';
            } else {
                $query->where('action_taken', $request->input('action_taken'));
                $activeFilters[] = 'Tindakan: '.$request->input('action_taken');
            }
        }

        if ($request->filled('category')) {
            $query->where('damage_category', $request->input('category'));
            $activeFilters[] = 'Kategori: '.$request->input('category');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reported_at', '>=', $request->input('date_from'));
            $activeFilters[] = 'Dari: '.$request->input('date_from');
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reported_at', '<=', $request->input('date_to'));
            $activeFilters[] = 'Sampai: '.$request->input('date_to');
        }

        $damagedGoods = $query->latest('reported_at')->limit(self::MAX_EXPORT_ROWS)->get();
        $type = strtolower($request->input('type', 'excel'));

        Log::info('security.report_exported', [
            'report' => 'damaged_goods',
            'format' => $type,
            'rows' => $damagedGoods->count(),
            'user_id' => Auth::id(),
        ]);

        // 1. Ekspor ke PDF
        if ($type === 'pdf') {
            $filename = 'Laporan_Barang_Rusak_'.now()->format('Y-m-d_His').'.pdf';
            $pdf = Pdf::loadView('exports.pdf.damaged-goods', compact('damagedGoods', 'activeFilters'))
                ->setPaper('a4', 'landscape');

            return $pdf->download($filename);
        }

        // 2. Ekspor ke Excel (.xlsx)
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Barang Rusak Karantina');

        $headers = [
            'No',
            'Kode Karantina',
            'Tanggal & Waktu Lapor',
            'SKU Barang',
            'Nama Perangkat',
            'Serial Number',
            'MAC Address',
            'Qty Rusak',
            'Satuan',
            'Kategori Kerusakan',
            'Pelapor / Teknisi',
            'Tipe Pelapor',
            'Keterangan Gejala Kerusakan',
            'Status Keputusan Tindakan',
            'Tanggal Selesai Tindakan',
            'Diselesaikan Oleh',
            'Catatan Tindakan',
            'Estimasi Nilai Kerugian (Rp)',
        ];

        $dataRows = [];
        $no = 1;
        foreach ($damagedGoods as $dmg) {
            $dataRows[] = [
                $no++,
                $dmg->code,
                $dmg->reported_at->format('Y-m-d H:i:s'),
                $dmg->product?->sku ?? '-',
                $dmg->product?->name ?? '-',
                $dmg->serial_number ?: '-',
                $dmg->mac_address ?: '-',
                (float) $dmg->qty,
                $dmg->product?->unit?->abbreviation ?? '-',
                $dmg->damage_category?->value ?? '-',
                $dmg->reported_by ?? '-',
                $dmg->reporter_type?->value ?? '-',
                $dmg->damage_description ?? '-',
                $dmg->action_taken?->value ?? '-',
                $dmg->action_date ? $dmg->action_date->format('Y-m-d H:i:s') : '-',
                $dmg->resolvedBy?->name ?? '-',
                $dmg->action_notes ?? '-',
                (float) $dmg->loss_cost,
            ];
        }

        $this->buildExcelSheet($sheet, 'LAPORAN BARANG RUSAK & AREA KARANTINA', $headers, $dataRows, [
            8 => '#,##0',
            18 => '#,##0',
        ]);

        $filename = 'Laporan_Barang_Rusak_'.now()->format('Y-m-d_His').'.xlsx';

        return $this->downloadExcelResponse($spreadsheet, $filename);
    }

    /**
     * Helper untuk styling dan pengisian data pada worksheet Excel (.xlsx)
     */
    private function buildExcelSheet(Worksheet $sheet, string $reportTitle, array $headers, array $dataRows, array $numberFormatColumns = []): void
    {
        $colCount = count($headers);
        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);

        // 1. Header Banner Perusahaan
        $sheet->setCellValue('A1', 'PT GAYUH MEDIA INFORMATIKA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF059669');

        $sheet->setCellValue('A2', $reportTitle.' • Dicetak: '.now()->translatedFormat('d F Y, H:i').' WIB');
        $sheet->getStyle('A2')->getFont()->setSize(9)->getColor()->setARGB('FF64748B');

        // 2. Baris Header Tabel (Row 4)
        $headerRow = 4;
        foreach ($headers as $colIndex => $headerText) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->setCellValue($colLetter.$headerRow, $headerText);
        }

        $headerRange = "A{$headerRow}:{$lastColLetter}{$headerRow}";
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 9.5,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF059669'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF047857'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        // 3. Baris Data (Row 5 onwards)
        $startRow = 5;
        $currentRow = $startRow;

        foreach ($dataRows as $row) {
            foreach ($row as $colIndex => $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex + 1);
                if (is_int($val) || is_float($val)) {
                    $sheet->setCellValueExplicit($colLetter.$currentRow, $val, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValueExplicit(
                        $colLetter.$currentRow,
                        $this->sanitizeExcelString((string) ($val ?? '')),
                        DataType::TYPE_STRING
                    );
                }
            }

            // Zebra striping untuk baris genap
            if ($currentRow % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFF8FAFC'],
                ]);
            }

            $currentRow++;
        }

        $lastDataRow = max($startRow, $currentRow - 1);
        $dataRange = "A{$startRow}:{$lastColLetter}{$lastDataRow}";

        if (! empty($dataRows)) {
            $sheet->getStyle($dataRange)->applyFromArray([
                'font' => [
                    'size' => 9,
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE2E8F0'],
                    ],
                ],
            ]);

            // Number formatting for specific columns
            foreach ($numberFormatColumns as $colIdx => $format) {
                $colLetter = Coordinate::stringFromColumnIndex($colIdx);
                $sheet->getStyle("{$colLetter}{$startRow}:{$colLetter}{$lastDataRow}")
                    ->getNumberFormat()
                    ->setFormatCode($format);
                $sheet->getStyle("{$colLetter}{$startRow}:{$colLetter}{$lastDataRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }

            // Center align No column
            $sheet->getStyle("A{$startRow}:A{$lastDataRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 4. Auto-fit column widths
        for ($i = 1; $i <= $colCount; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
    }

    private function sanitizeExcelString(string $value): string
    {
        if ($value === '' || $value === '-') {
            return $value;
        }

        if (preg_match('/^[=+\-@]/', ltrim($value, " \t\r\n")) === 1 || preg_match('/^[\t\r\n]/', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }

    /**
     * Response stream download file Microsoft Excel (.xlsx)
     */
    private function downloadExcelResponse(Spreadsheet $spreadsheet, string $filename): Response
    {
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }
}
