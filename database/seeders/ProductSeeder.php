<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catAct = Category::where('code', 'ACT-DEV')->first()->id;
        $catPas = Category::where('code', 'PAS-MAT')->first()->id;
        $catAcc = Category::where('code', 'ACC-FO')->first()->id;
        $catTools = Category::where('code', 'TOOLS')->first()->id;
        $catPop = Category::where('code', 'POP-RACK')->first()->id;

        $unitUnit = Unit::where('abbreviation', 'Unit')->first()->id;
        $unitPcs = Unit::where('abbreviation', 'Pcs')->first()->id;
        $unitMeter = Unit::where('abbreviation', 'M')->first()->id;
        $unitRoll = Unit::where('abbreviation', 'Roll')->first()->id;
        $unitPack = Unit::where('abbreviation', 'Pack')->first()->id;
        $unitSet = Unit::where('abbreviation', 'Set')->first()->id;

        $locRakA01 = Location::where('code', 'RAK-A01')->first()->id;
        $locRakA02 = Location::where('code', 'RAK-A02')->first()->id;
        $locRakB01 = Location::where('code', 'RAK-B01')->first()->id;
        $locBin01 = Location::where('code', 'BIN-FIBER01')->first()->id;
        $locPallet01 = Location::where('code', 'PALLET-C01')->first()->id;
        $locRakD01 = Location::where('code', 'RAK-D01')->first()->id;

        $products = [
            // Perangkat Aktif
            [
                'sku' => 'ONT-ZTE-F609',
                'barcode' => '899100100001',
                'name' => 'ONT GPON ZTE F609 V5.3 Dual Band AC1200',
                'category_id' => $catAct,
                'unit_id' => $unitUnit,
                'location_id' => $locRakA01,
                'stock' => 28.00,
                'damaged_stock' => 4.00,
                'min_stock' => 10.00,
                'cost_price' => 285000.00,
                'has_serial_number' => true,
                'specification' => 'GPON ONU ONT, Dual Band 2.4GHz & 5GHz, 4 Gigabit Ethernet Port, 1 Voice POTS RJ11, 1 USB 2.0. Redaman optimal: -8 s/d -25 dBm.',
                'stock_notes' => 'Wajib cek redaman RX/TX dan pastikan firmware default GayuhNet sebelum diserahkan ke teknisi PSB.',
            ],
            [
                'sku' => 'ONT-HW-HG8546M',
                'barcode' => '899100100002',
                'name' => 'ONT GPON Huawei EchoLife HG8546M Single Band',
                'category_id' => $catAct,
                'unit_id' => $unitUnit,
                'location_id' => $locRakA01,
                'stock' => 15.00,
                'damaged_stock' => 2.00,
                'min_stock' => 10.00,
                'cost_price' => 195000.00,
                'has_serial_number' => true,
                'specification' => 'EPON/GPON ONU, 1 GE + 3 FE Ports, 1 POTS, 2.4GHz Wi-Fi MIMO 2x2 300Mbps. SC-UPC connector.',
                'stock_notes' => 'Cocok untuk paket pelanggan hemat / low-budget 20-30 Mbps. Uji port LAN sebelum serah terima.',
            ],
            [
                'sku' => 'RTR-RB750GR3',
                'barcode' => '899100100003',
                'name' => 'Routerboard MikroTik RB750Gr3 (hEX)',
                'category_id' => $catAct,
                'unit_id' => $unitUnit,
                'location_id' => $locRakA02,
                'stock' => 8.00,
                'damaged_stock' => 1.00,
                'min_stock' => 5.00,
                'cost_price' => 740000.00,
                'has_serial_number' => true,
                'specification' => 'Dual Core 880MHz, 256MB RAM, 5x Gigabit LAN 10/100/1000, MicroSD slot, RouterOS v7 Level 4 License.',
                'stock_notes' => 'Router gateway standar pelanggan corporate / UMKM. Serial number tertera di stiker bawah casing.',
            ],
            [
                'sku' => 'RTR-CCR2004',
                'barcode' => '899100100004',
                'name' => 'Routerboard MikroTik CCR2004-16G-2S+PC',
                'category_id' => $catAct,
                'unit_id' => $unitUnit,
                'location_id' => $locRakA02,
                'stock' => 2.00,
                'damaged_stock' => 0.00,
                'min_stock' => 1.00,
                'cost_price' => 7250000.00,
                'has_serial_number' => true,
                'specification' => '4-Core AL32400 1.7GHz, 4GB DDR4 RAM, 16x Gigabit Ethernet, 2x 10G SFP+ Cages, Passive cooling enclosure.',
                'stock_notes' => 'Aset kritikal PoP Backbone & Aggregation. Pengambilan wajib atas persetujuan Lead Network Engineer.',
            ],
            [
                'sku' => 'OLT-ZTE-C320',
                'barcode' => '899100100005',
                'name' => 'OLT GPON ZTE ZXA10 C320 + GTGO 8-Port Card SFP C+',
                'category_id' => $catPop,
                'unit_id' => $unitUnit,
                'location_id' => $locRakA02,
                'stock' => 1.00,
                'damaged_stock' => 0.00,
                'min_stock' => 1.00,
                'cost_price' => 24500000.00,
                'has_serial_number' => true,
                'specification' => 'Chassis 2U OLT Compact, Card GTGO 8 Port PON SFP C+ (Max 1:128 Splitter per port = 1024 ONT), Dual Power DC 48V.',
                'stock_notes' => 'Unit backup darurat PoP Pusat. Simpan di ruang ber-AC dan tutup dust-cover pada port SFP yang belum terpakai.',
            ],
            [
                'sku' => 'SFP-BIDI-1G',
                'barcode' => '899100100006',
                'name' => 'SFP Transceiver 1.25G Single Mode BiDi 20km Tx1310/Rx1550nm',
                'category_id' => $catAct,
                'unit_id' => $unitPcs,
                'location_id' => $locRakB01,
                'stock' => 32.00,
                'damaged_stock' => 3.00,
                'min_stock' => 10.00,
                'cost_price' => 95000.00,
                'has_serial_number' => true,
                'specification' => 'BiDirectional Single Fiber SC Simplex 1.25Gbps, Jarak jangkauan 20 km, Laser Class 1, Hot-pluggable.',
                'stock_notes' => 'Wajib pasang dust cap saat disimpan. Sensitif terhadap debu dan goresan pada permukaan ferrule optik.',
            ],

            // Material Pasif & Kabel FO
            [
                'sku' => 'CBL-DC-1C-1000',
                'barcode' => '899100100007',
                'name' => 'Kabel Drop Core FO 1 Core 3 Steel Wire (Roll 1000M)',
                'category_id' => $catPas,
                'unit_id' => $unitRoll,
                'location_id' => $locPallet01,
                'stock' => 14.00,
                'damaged_stock' => 0.00,
                'min_stock' => 5.00,
                'cost_price' => 550000.00,
                'has_serial_number' => false,
                'specification' => 'Fiber Optic Drop Cable 1 Core G.657A1 LSZH, 3 Kawat Baja Penguat (Messenger Wire 1.0mm + 2 Side FRP Wire 0.45mm). Roll 1000 meter.',
                'stock_notes' => 'Pastikan drum kayu/karton tidak tertindih beban berlebih di pallet. Jangan sampai kabel tertekuk tajam (kinked).',
            ],
            [
                'sku' => 'CBL-DC-1C-MTR',
                'barcode' => '899100100008',
                'name' => 'Kabel Drop Core FO 1 Core Eceran / Tarikan (Meter)',
                'category_id' => $catPas,
                'unit_id' => $unitMeter,
                'location_id' => $locPallet01,
                'stock' => 450.00,
                'damaged_stock' => 0.00,
                'min_stock' => 200.00,
                'cost_price' => 600.00,
                'has_serial_number' => false,
                'specification' => 'Kabel dropcore eceran untuk keperluan PSB meteran dan tarikan pendek pelanggan dekat ODP.',
                'stock_notes' => 'Catat meteran awal dan sisa meteran pada rol potongan.',
            ],
            [
                'sku' => 'CON-FAST-SCUPC',
                'barcode' => '899100100009',
                'name' => 'Fast Connector SC-UPC Biru (Pack 100 pcs)',
                'category_id' => $catPas,
                'unit_id' => $unitPack,
                'location_id' => $locBin01,
                'stock' => 18.00,
                'damaged_stock' => 1.00,
                'min_stock' => 5.00,
                'cost_price' => 145000.00,
                'has_serial_number' => false,
                'specification' => 'Fast Connector Cold Splice SC-UPC (Warna Biru), Insertion Loss <= 0.3dB, Return Loss >= 50dB.',
                'stock_notes' => 'Jika teknisi gagal pasang (redaman tinggi), wajib dikumpulkan untuk evaluasi apakah cacat batch pabrik.',
            ],
            [
                'sku' => 'CON-FAST-SCAPC',
                'barcode' => '899100100010',
                'name' => 'Fast Connector SC-APC Hijau (Pack 100 pcs)',
                'category_id' => $catPas,
                'unit_id' => $unitPack,
                'location_id' => $locBin01,
                'stock' => 4.00,
                'damaged_stock' => 0.00,
                'min_stock' => 5.00, // Trigger LOW stock badge!
                'cost_price' => 165000.00,
                'has_serial_number' => false,
                'specification' => 'Fast Connector SC-APC Angled Physical Contact (Warna Hijau), Return Loss >= 60dB.',
                'stock_notes' => 'Stok menipis, segera ajukan pengadaan ulang ke PT Optik Distribusi Nusantara.',
            ],
            [
                'sku' => 'PTC-SC-3M',
                'barcode' => '899100100011',
                'name' => 'Patch Cord Fiber Optic SC-UPC to SC-UPC Simplex 3 Meter',
                'category_id' => $catPas,
                'unit_id' => $unitPcs,
                'location_id' => $locRakB01,
                'stock' => 85.00,
                'damaged_stock' => 6.00,
                'min_stock' => 20.00,
                'cost_price' => 12500.00,
                'has_serial_number' => false,
                'specification' => 'Yellow Jacket PVC 3.0mm, Single Mode 9/125um, Panjang 3 Meter, SC to SC UPC Polish.',
                'stock_notes' => 'Jangan ditarik paksa saat mengeluarkan dari bundle. Hindari tekukan radius < 30mm.',
            ],
            [
                'sku' => 'ODP-POLE-8C',
                'barcode' => '899100100012',
                'name' => 'Optical Distribution Point (ODP) 8 Core Solid Tiang',
                'category_id' => $catPas,
                'unit_id' => $unitPcs,
                'location_id' => $locPallet01,
                'stock' => 12.00,
                'damaged_stock' => 0.00,
                'min_stock' => 5.00,
                'cost_price' => 175000.00,
                'has_serial_number' => false,
                'specification' => 'Enclosure Outdoor IP65 Anti-UV, Kapasitas 8 Port SC Adapter, Tray Splicing 12 Core, Lengkap Bracket Tiang & Kunci.',
                'stock_notes' => 'Pastikan karet seal waterproof lengkap di dalam box sebelum diserahkan ke tim pembangunan jaringan.',
            ],
            [
                'sku' => 'SLV-PROT-60',
                'barcode' => '899100100013',
                'name' => 'Protection Sleeve Sambungan Splicing 60mm (Pack 100 pcs)',
                'category_id' => $catPas,
                'unit_id' => $unitPack,
                'location_id' => $locBin01,
                'stock' => 25.00,
                'damaged_stock' => 0.00,
                'min_stock' => 10.00,
                'cost_price' => 35000.00,
                'has_serial_number' => false,
                'specification' => 'Selongsong bakar sambungan FO 60mm dengan jarum stainless steel pengaku anti bengkok.',
                'stock_notes' => 'Material habis pakai untuk teknisi splicing.',
            ],
            [
                'sku' => 'CLP-HOOK-S',
                'barcode' => '899100100014',
                'name' => 'Clamp Hook Tarikan Drop Core Model S Galvanis',
                'category_id' => $catAcc,
                'unit_id' => $unitPcs,
                'location_id' => $locBin01,
                'stock' => 350.00,
                'damaged_stock' => 0.00,
                'min_stock' => 100.00,
                'cost_price' => 2800.00,
                'has_serial_number' => false,
                'specification' => 'Klem pengait kabel dropcore model S besi lapis galvanis anti-karat outdoor.',
                'stock_notes' => 'Item wajib untuk instalasi PSB rute tiang PLN/Telkom ke rumah pelanggan.',
            ],

            // Perkakas Kerja & Alat Ukur
            [
                'sku' => 'TOOL-SPLICER-88S',
                'barcode' => '899100100015',
                'name' => 'Fusion Splicer Fujikura 88S+ Core Alignment Flagship',
                'category_id' => $catTools,
                'unit_id' => $unitSet,
                'location_id' => $locRakD01,
                'stock' => 3.00,
                'damaged_stock' => 0.00,
                'min_stock' => 1.00,
                'cost_price' => 62000000.00,
                'has_serial_number' => true,
                'specification' => 'Penyambung Fiber Optic Core-to-Core Alignment, Waktu Splicing 5-7 detik, Pemanas Oven 9-13 detik, Touchscreen, Bluetooth.',
                'stock_notes' => 'Aset bernilai sangat tinggi! Cek elektroda, baterai, cleaver CT-50, dan kebersihan V-Groove sebelum dan sesudah dipinjam.',
            ],
            [
                'sku' => 'TOOL-OTDR-ANRITSU',
                'barcode' => '899100100016',
                'name' => 'OTDR Anritsu MT9090A Micro-OTDR 1310/1550nm PON',
                'category_id' => $catTools,
                'unit_id' => $unitSet,
                'location_id' => $locRakD01,
                'stock' => 2.00,
                'damaged_stock' => 0.00,
                'min_stock' => 1.00,
                'cost_price' => 38000000.00,
                'has_serial_number' => true,
                'specification' => 'Alat ukur pantulan & titik putus kabel optik, Dynamic Range 32/30dB, Event Dead Zone 0.8m, Built-in OPM, Stabilized Light Source.',
                'stock_notes' => 'Wajib menggunakan launch cable / dummy reel saat mengukur fiber agar connector port tidak aus.',
            ],
            [
                'sku' => 'TOOL-OPM-VFL',
                'barcode' => '899100100017',
                'name' => 'Optical Power Meter (-70 to +10 dBm) & VFL Senter Optik 20mW',
                'category_id' => $catTools,
                'unit_id' => $unitUnit,
                'location_id' => $locRakD01,
                'stock' => 12.00,
                'damaged_stock' => 2.00,
                'min_stock' => 5.00,
                'cost_price' => 275000.00,
                'has_serial_number' => false,
                'specification' => 'Pengukur redaman optik digital kalibrasi 850/1300/1310/1490/1550/1625nm + Laser merah Visual Fault Locator 20mW jangkauan 20km.',
                'stock_notes' => 'Cek fungsi baterai AA dan kalibrasi nol sebelum diberikan ke teknisi maintenance.',
            ],
            [
                'sku' => 'TOOL-CLEAVER-CT50',
                'barcode' => '899100100018',
                'name' => 'High Precision Optical Fiber Cleaver Fujikura CT-50',
                'category_id' => $catTools,
                'unit_id' => $unitUnit,
                'location_id' => $locRakD01,
                'stock' => 4.00,
                'damaged_stock' => 1.00,
                'min_stock' => 2.00,
                'cost_price' => 8500000.00,
                'has_serial_number' => true,
                'specification' => 'Pemotong presisi core FO sudut < 0.5 derajat, Blade life 60,000 cleaves, Wireless blade rotation capability.',
                'stock_notes' => 'Sensitif terhadap guncangan atau jatuh. Selalu simpan di hardcase pelindung.',
            ],
            [
                'sku' => 'SFP-10G-LR',
                'barcode' => '899100100019',
                'name' => 'SFP+ Transceiver 10GBASE-LR 1310nm 10km LC Duplex',
                'category_id' => $catAct,
                'unit_id' => $unitPcs,
                'location_id' => $locRakB01,
                'stock' => 0.00, // Trigger EMPTY stock badge!
                'damaged_stock' => 1.00,
                'min_stock' => 4.00,
                'cost_price' => 450000.00,
                'has_serial_number' => true,
                'specification' => '10Gbps SFP+ Optical Module LC Duplex Single Mode 10km 1310nm DDM.',
                'stock_notes' => 'Stok HABIS di gudang! Ada 1 unit rusak di karantina menunggu klaim garansi vendor.',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['sku' => $p['sku']], $p);
        }
    }
}
