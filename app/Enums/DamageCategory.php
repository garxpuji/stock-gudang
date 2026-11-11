<?php

namespace App\Enums;

enum DamageCategory: string
{
    case LIGHTNING_STRIKE = 'Tersambar Petir / Overvoltage';
    case PORT_DAMAGED = 'Port LAN/Fiber Rusak';
    case LASER_DROP = 'Optic Laser Drop';
    case PHYSICAL_DEFECT = 'Fisik Pecah / Cacat';
    case TOTAL_DEAD = 'Mati Total';

    public function label(): string
    {
        return $this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::LIGHTNING_STRIKE => 'bg-amber-50 text-amber-800 border-amber-300',
            self::PORT_DAMAGED => 'bg-orange-50 text-orange-800 border-orange-300',
            self::LASER_DROP => 'bg-sky-50 text-sky-800 border-sky-300',
            self::PHYSICAL_DEFECT => 'bg-purple-50 text-purple-800 border-purple-300',
            self::TOTAL_DEAD => 'bg-rose-50 text-rose-800 border-rose-300',
        };
    }
}
