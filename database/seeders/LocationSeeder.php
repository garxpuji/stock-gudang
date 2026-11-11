<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'code' => 'RAK-A01',
                'name' => 'Rak A-01 (ONT & Modem Pelanggan)',
                'description' => 'Area rak depan untuk ONT Huawei, ZTE, Fiberhome siap pakai PSB.',
            ],
            [
                'code' => 'RAK-A02',
                'name' => 'Rak A-02 (MikroTik & Core Switch)',
                'description' => 'Rak penyimpanan Routerboard MikroTik hEX, CCR, CRS, dan switch gigabit.',
            ],
            [
                'code' => 'RAK-B01',
                'name' => 'Rak B-01 (SFP Module & Patch Cord)',
                'description' => 'Laci khusus modul optik SFP/SFP+ BiDi 1.25G/10G dan patch cord SC/LC.',
            ],
            [
                'code' => 'BIN-FIBER01',
                'name' => 'Bin Fiber-01 (Fast Connector & Sleeve)',
                'description' => 'Bin kompartemen kecil untuk fast connector SC/UPC, pigtail, dan protection sleeve.',
            ],
            [
                'code' => 'PALLET-C01',
                'name' => 'Pallet C-01 (Roll Drop Core FO)',
                'description' => 'Lantai pallet utama untuk drum / roll kabel dropcore FO 1 Core & 2 Core (1000M).',
            ],
            [
                'code' => 'RAK-D01',
                'name' => 'Rak D-01 (Alat Ukur: Splicer & OTDR)',
                'description' => 'Lemari besi berkunci untuk Fusion Splicer, OTDR, Optical Power Meter, dan VFL.',
            ],
            [
                'code' => 'KARANTINA-01',
                'name' => 'Area Karantina & Barang Rusak',
                'description' => 'Area isolasi perangkat rusak kena petir, port terbakar, atau menunggu retur RMA vendor.',
            ],
        ];

        foreach ($locations as $loc) {
            Location::updateOrCreate(['code' => $loc['code']], $loc);
        }
    }
}
