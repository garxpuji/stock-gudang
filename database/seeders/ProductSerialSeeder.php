<?php

namespace Database\Seeders;

use App\Enums\SerialStatus;
use App\Models\Product;
use App\Models\ProductSerial;
use Illuminate\Database\Seeder;

class ProductSerialSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ONT ZTE F609
        $zte = Product::where('sku', 'ONT-ZTE-F609')->first();
        if ($zte) {
            $serialsZte = [
                ['sn' => 'ZTEGCA881001', 'mac' => '48:D2:24:A1:01:01', 'status' => SerialStatus::AVAILABLE],
                ['sn' => 'ZTEGCA881002', 'mac' => '48:D2:24:A1:01:02', 'status' => SerialStatus::AVAILABLE],
                ['sn' => 'ZTEGCA881003', 'mac' => '48:D2:24:A1:01:03', 'status' => SerialStatus::AVAILABLE],
                ['sn' => 'ZTEGCA881004', 'mac' => '48:D2:24:A1:01:04', 'status' => SerialStatus::AVAILABLE],
                ['sn' => 'ZTEGCA881005', 'mac' => '48:D2:24:A1:01:05', 'status' => SerialStatus::AVAILABLE],
                ['sn' => 'ZTEGCA881006', 'mac' => '48:D2:24:A1:01:06', 'status' => SerialStatus::ISSUED, 'rec' => 'Ahmad Wahyudi (Tim Fiber 1)', 'tkt' => 'SPK-PSB-202611-042'],
                ['sn' => 'ZTEGCA881007', 'mac' => '48:D2:24:A1:01:07', 'status' => SerialStatus::ISSUED, 'rec' => 'Rian Hidayat (Maintenance)', 'tkt' => 'TKT-MTN-202611-109'],
                ['sn' => 'ZTEGCA881008', 'mac' => '48:D2:24:A1:01:08', 'status' => SerialStatus::DAMAGED, 'notes' => 'Port LAN 1-4 hangus tersambar petir di rumah pelanggan'],
                ['sn' => 'ZTEGCA881009', 'mac' => '48:D2:24:A1:01:09', 'status' => SerialStatus::DAMAGED, 'notes' => 'Optic laser drop (RX redaman loss)'],
                ['sn' => 'ZTEGCA881010', 'mac' => '48:D2:24:A1:01:10', 'status' => SerialStatus::RMA, 'notes' => 'Dikirim ke vendor PT Fiberhome untuk klaim garansi'],
            ];

            foreach ($serialsZte as $item) {
                ProductSerial::updateOrCreate(
                    ['product_id' => $zte->id, 'serial_number' => $item['sn']],
                    [
                        'mac_address' => $item['mac'],
                        'status' => $item['status'],
                        'last_recipient' => $item['rec'] ?? null,
                        'last_ticket_no' => $item['tkt'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]
                );
            }
        }

        // 2. ONT Huawei HG8546M
        $hw = Product::where('sku', 'ONT-HW-HG8546M')->first();
        if ($hw) {
            $serialsHw = [
                ['sn' => '48575443B101', 'mac' => '00:E0:FC:88:11:01', 'status' => SerialStatus::AVAILABLE],
                ['sn' => '48575443B102', 'mac' => '00:E0:FC:88:11:02', 'status' => SerialStatus::AVAILABLE],
                ['sn' => '48575443B103', 'mac' => '00:E0:FC:88:11:03', 'status' => SerialStatus::AVAILABLE],
                ['sn' => '48575443B104', 'mac' => '00:E0:FC:88:11:04', 'status' => SerialStatus::ISSUED, 'rec' => 'Ahmad Wahyudi (Tim Fiber 1)', 'tkt' => 'SPK-PSB-202611-043'],
                ['sn' => '48575443B105', 'mac' => '00:E0:FC:88:11:05', 'status' => SerialStatus::DAMAGED, 'notes' => 'Mati Total setelah lonjakan voltase PLN'],
            ];

            foreach ($serialsHw as $item) {
                ProductSerial::updateOrCreate(
                    ['product_id' => $hw->id, 'serial_number' => $item['sn']],
                    [
                        'mac_address' => $item['mac'],
                        'status' => $item['status'],
                        'last_recipient' => $item['rec'] ?? null,
                        'last_ticket_no' => $item['tkt'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]
                );
            }
        }

        // 3. MikroTik hEX
        $hex = Product::where('sku', 'RTR-RB750GR3')->first();
        if ($hex) {
            $serialsHex = [
                ['sn' => '6E0807C4A001', 'mac' => 'CC:2D:E0:51:A0:01', 'status' => SerialStatus::AVAILABLE],
                ['sn' => '6E0807C4A002', 'mac' => 'CC:2D:E0:51:A0:02', 'status' => SerialStatus::AVAILABLE],
                ['sn' => '6E0807C4A003', 'mac' => 'CC:2D:E0:51:A0:03', 'status' => SerialStatus::ISSUED, 'rec' => 'Dani Ramadhan (NOC Shift Siang)', 'tkt' => 'INC-NOC-202611-018'],
            ];

            foreach ($serialsHex as $item) {
                ProductSerial::updateOrCreate(
                    ['product_id' => $hex->id, 'serial_number' => $item['sn']],
                    [
                        'mac_address' => $item['mac'],
                        'status' => $item['status'],
                        'last_recipient' => $item['rec'] ?? null,
                        'last_ticket_no' => $item['tkt'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]
                );
            }
        }

        // 4. MikroTik CCR2004
        $ccr = Product::where('sku', 'RTR-CCR2004')->first();
        if ($ccr) {
            ProductSerial::updateOrCreate(
                ['product_id' => $ccr->id, 'serial_number' => 'CCR2004-9812A'],
                [
                    'mac_address' => '48:8F:5A:CC:20:01',
                    'status' => SerialStatus::AVAILABLE,
                    'notes' => 'Unit cadangan PoP Pusat Gayuh Media Informatika',
                ]
            );
            ProductSerial::updateOrCreate(
                ['product_id' => $ccr->id, 'serial_number' => 'CCR2004-9812B'],
                [
                    'mac_address' => '48:8F:5A:CC:20:02',
                    'status' => SerialStatus::AVAILABLE,
                    'notes' => 'Unit cadangan PoP Barat',
                ]
            );
        }

        // 5. Fusion Splicer Fujikura 88S+
        $splicer = Product::where('sku', 'TOOL-SPLICER-88S')->first();
        if ($splicer) {
            $serialsSplicer = [
                ['sn' => 'FS88S-JKT-01', 'status' => SerialStatus::AVAILABLE, 'notes' => 'Kondisi elektroda 420/5000 splices, siap pakai tim malam'],
                ['sn' => 'FS88S-JKT-02', 'status' => SerialStatus::ISSUED, 'rec' => 'Ahmad Wahyudi (Tim Fiber 1)', 'tkt' => 'SPK-PSB-202611-042', 'notes' => 'Dipinjam untuk penarikan feeder ODP RW 05'],
                ['sn' => 'FS88S-JKT-03', 'status' => SerialStatus::AVAILABLE, 'notes' => 'Standby di lemari alat D-01'],
            ];

            foreach ($serialsSplicer as $item) {
                ProductSerial::updateOrCreate(
                    ['product_id' => $splicer->id, 'serial_number' => $item['sn']],
                    [
                        'status' => $item['status'],
                        'last_recipient' => $item['rec'] ?? null,
                        'last_ticket_no' => $item['tkt'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]
                );
            }
        }

        // 6. Anritsu OTDR
        $otdr = Product::where('sku', 'TOOL-OTDR-ANRITSU')->first();
        if ($otdr) {
            ProductSerial::updateOrCreate(
                ['product_id' => $otdr->id, 'serial_number' => 'ANR-MT9090-8801'],
                [
                    'status' => SerialStatus::AVAILABLE,
                    'notes' => 'Kalibrasi aktif s/d Des 2027. Dilengkapi launch fiber 500M SC-UPC.',
                ]
            );
            ProductSerial::updateOrCreate(
                ['product_id' => $otdr->id, 'serial_number' => 'ANR-MT9090-8802'],
                [
                    'status' => SerialStatus::ISSUED,
                    'last_recipient' => 'Rian Hidayat (Maintenance)',
                    'last_ticket_no' => 'TKT-MTN-202611-109',
                    'notes' => 'Dipakai pelacakan bending optik di segmen jalan raya timur',
                ]
            );
        }
    }
}
