<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Unit', 'abbreviation' => 'Unit'],
            ['name' => 'Pieces', 'abbreviation' => 'Pcs'],
            ['name' => 'Meter', 'abbreviation' => 'M'],
            ['name' => 'Roll (1000m)', 'abbreviation' => 'Roll'],
            ['name' => 'Set', 'abbreviation' => 'Set'],
            ['name' => 'Box', 'abbreviation' => 'Box'],
            ['name' => 'Pack (100 pcs)', 'abbreviation' => 'Pack'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(['abbreviation' => $unit['abbreviation']], $unit);
        }
    }
}
