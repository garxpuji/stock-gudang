<?php

namespace App\Enums;

enum StockStatus: string
{
    case SAFE = 'SAFE';
    case LOW = 'LOW';
    case EMPTY = 'EMPTY';
    case QUARANTINED = 'QUARANTINED';

    public function label(): string
    {
        return match ($this) {
            self::SAFE => 'Aman / Tersedia',
            self::LOW => 'Menipis',
            self::EMPTY => 'Habis',
            self::QUARANTINED => 'Karantina / Rusak',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::SAFE => 'bg-emerald-50 text-emerald-700 border-emerald-300 ring-emerald-500/20',
            self::LOW => 'bg-amber-50 text-amber-700 border-amber-300 ring-amber-500/20',
            self::EMPTY => 'bg-rose-50 text-rose-700 border-rose-300 ring-rose-500/20',
            self::QUARANTINED => 'bg-orange-50 text-orange-700 border-orange-300 ring-orange-500/20',
        };
    }

    public function dotColor(): string
    {
        return match ($this) {
            self::SAFE => 'bg-emerald-500',
            self::LOW => 'bg-amber-500',
            self::EMPTY => 'bg-rose-500',
            self::QUARANTINED => 'bg-orange-500',
        };
    }
}
