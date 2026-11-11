<?php

namespace App\Enums;

enum QcStatus: string
{
    case GOOD = 'GOOD';
    case DAMAGED = 'DAMAGED';

    public function label(): string
    {
        return match ($this) {
            self::GOOD => 'Baik / Layak Pakai (Restock)',
            self::DAMAGED => 'Rusak / Cacat (Karantina)',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::GOOD => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::DAMAGED => 'bg-rose-50 text-rose-700 border-rose-200',
        };
    }
}
