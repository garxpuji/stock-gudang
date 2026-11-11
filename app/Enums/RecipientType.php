<?php

namespace App\Enums;

enum RecipientType: string
{
    case TEKNISI = 'TEKNISI';
    case KARYAWAN = 'KARYAWAN';

    public function label(): string
    {
        return match ($this) {
            self::TEKNISI => 'Teknisi Lapangan (PSB / Maintenance)',
            self::KARYAWAN => 'Karyawan / Divisi Internal',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::TEKNISI => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::KARYAWAN => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        };
    }
}
