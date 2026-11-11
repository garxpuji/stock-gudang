<?php

namespace App\Enums;

enum DamagedActionTaken: string
{
    case PENDING = 'PENDING';
    case RMA_VENDOR = 'RMA_VENDOR';
    case SCRAPPED = 'SCRAPPED';
    case REPAIRED_INTERNAL = 'REPAIRED_INTERNAL';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Karantina / Menunggu Tindakan',
            self::RMA_VENDOR => 'Klaim Garansi / Retur Vendor (RMA)',
            self::SCRAPPED => 'Pemusnahan / Write-Off (Scrapped)',
            self::REPAIRED_INTERNAL => 'Perbaikan Internal (In-House Repair)',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-50 text-amber-700 border-amber-200',
            self::RMA_VENDOR => 'bg-purple-50 text-purple-700 border-purple-200',
            self::SCRAPPED => 'bg-rose-50 text-rose-700 border-rose-200',
            self::REPAIRED_INTERNAL => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        };
    }
}
