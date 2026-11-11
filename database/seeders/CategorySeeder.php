<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'code' => 'ACT-DEV',
                'name' => 'Perangkat Aktif (Tracked SN/MAC)',
                'description' => 'ONT/ONU, Routerboard, OLT, SFP Transceiver, Managed Switch yang wajib tercatat Serial Number & MAC Address per unit.',
            ],
            [
                'code' => 'PAS-MAT',
                'name' => 'Material Pasif & Kabel FO',
                'description' => 'Drop Core FO (1C/2C), Patch Cord, Fast Connector, Protection Sleeve, ODP, OTB, Roset.',
            ],
            [
                'code' => 'ACC-FO',
                'name' => 'Aksesoris Tiang & Tarikan FO',
                'description' => 'Clamp Hook, Tensioner / Span Wartel, Stainless Steel Band, Suspension Clamp, Spiral Grip.',
            ],
            [
                'code' => 'TOOLS',
                'name' => 'Perkakas & Alat Ukur (Tools)',
                'description' => 'Fusion Splicer, OTDR, Optical Power Meter (OPM), Visual Fault Locator (VFL), Cleaver, Stripper.',
            ],
            [
                'code' => 'POP-RACK',
                'name' => 'Perangkat PoP & Datacenter',
                'description' => 'Chassis OLT, Server Rack Server, UPS Online, Rectifier DC 48V, Baterai VRLA, Patch Panel.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['code' => $cat['code']], $cat);
        }
    }
}
