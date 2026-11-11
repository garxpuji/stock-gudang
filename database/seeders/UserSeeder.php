<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gayuhmedia.id'],
            [
                'name' => 'Budi Santoso',
                'role' => 'admin_gudang',
                'phone' => '081234567890',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tegar@gayuhmedia.id'],
            [
                'name' => 'Tegar Pratama (Principal NOC)',
                'role' => 'superadmin',
                'phone' => '081298765432',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'gudang@gayuhmedia.id'],
            [
                'name' => 'Aris Munandar',
                'role' => 'admin_gudang',
                'phone' => '081377889900',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );
    }
}
