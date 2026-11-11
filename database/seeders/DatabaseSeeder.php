<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with Gayuh Media Informatika warehouse dataset.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            LocationSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ProductSerialSeeder::class,
            InitialTransactionsSeeder::class,
        ]);
    }
}
