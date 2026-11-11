<?php

namespace App\Enums;

enum SerialStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case ISSUED = 'ISSUED';
    case DAMAGED = 'DAMAGED';
    case RMA = 'RMA';
    case SCRAPPED = 'SCRAPPED';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Tersedia di Gudang',
            self::ISSUED => 'Dipakai Lapangan / Pelanggan',
            self::DAMAGED => 'Rusak / Karantina',
            self::RMA => 'Klaim Garansi (RMA)',
            self::SCRAPPED => 'Afkir / Dimusnahkan',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::ISSUED => 'bg-sky-50 text-sky-700 border-sky-200',
            self::DAMAGED => 'bg-orange-50 text-orange-700 border-orange-200',
            self::RMA => 'bg-purple-50 text-purple-700 border-purple-200',
            self::SCRAPPED => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
