<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code' => 'SUP-ZTE-FIBER',
                'name' => 'PT Fiberhome & ZTE Optic Network',
                'contact_name' => 'Hendri Wijaya',
                'phone' => '021-58901234',
                'email' => 'sales@ztefiberindo.co.id',
                'address' => 'Kawasan Industri MM2100 Blok C-4, Cikarang Barat, Bekasi',
            ],
            [
                'code' => 'SUP-MIKROTIK',
                'name' => 'PT Citraweb Solusi Informatika (MikroTik Master)',
                'contact_name' => 'Yusuf Arifin',
                'phone' => '0274-554433',
                'email' => 'order@citraweb.com',
                'address' => 'Jl. Petung No. 31 Papringan, Caturtunggal, Depok, Sleman, Yogyakarta',
            ],
            [
                'code' => 'SUP-OPTIC-DIST',
                'name' => 'PT Optik Distribusi Nusantara',
                'contact_name' => 'Susanto Salim',
                'phone' => '021-66778899',
                'email' => 'support@optikdistribusi.id',
                'address' => 'Komplek Harco Mangga Dua Blok B No. 18, Jakarta Pusat',
            ],
            [
                'code' => 'SUP-TELKO-TOOLS',
                'name' => 'CV Telko Sukses Mandiri (Tools & Splicer)',
                'contact_name' => 'Bambang Sudibyo',
                'phone' => '081122334455',
                'email' => 'sales@telkosukses.com',
                'address' => 'Ruko Grand Galaxy City Blok RRG 5 No. 12, Bekasi Selatan',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::updateOrCreate(['code' => $sup['code']], $sup);
        }
    }
}
