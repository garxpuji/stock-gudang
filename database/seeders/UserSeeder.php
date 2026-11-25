<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'email' => 'admin@gayuhmedia.id',
                'name' => 'Budi Santoso',
                'role' => 'admin_gudang',
                'phone' => '081234567890',
            ],
            [
                'email' => 'tegar@gayuhmedia.id',
                'name' => 'Tegar Pratama (Principal NOC)',
                'role' => 'superadmin',
                'phone' => '081298765432',
            ],
            [
                'email' => 'gudang@gayuhmedia.id',
                'name' => 'Aris Munandar',
                'role' => 'admin_gudang',
                'phone' => '081377889900',
            ],
        ];

        $configuredPassword = env('INITIAL_ADMIN_PASSWORD');

        foreach ($accounts as $account) {
            if (User::where('email', $account['email'])->exists()) {
                continue;
            }

            if (is_string($configuredPassword) && $configuredPassword !== '') {
                $plainPassword = $configuredPassword;
            } elseif (app()->environment(['local', 'testing'])) {
                $plainPassword = 'password';
            } else {
                $plainPassword = Str::password(16);
                $this->command?->warn("Akun awal dibuat [{$account['email']}] password sementara: {$plainPassword} — segera ganti setelah login!");
            }

            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'phone' => $account['phone'],
                    'password' => Hash::make($plainPassword),
                    'is_active' => true,
                ]
            );
        }
    }
}
