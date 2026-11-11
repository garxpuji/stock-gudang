<?php

namespace App\Enums;

enum MutationType: string
{
    case INBOUND = 'INBOUND';
    case OUTBOUND_TEKNISI = 'OUTBOUND_TEKNISI';
    case OUTBOUND_KARYAWAN = 'OUTBOUND_KARYAWAN';
    case RETURN_GOOD = 'RETURN_GOOD';
    case RETURN_DAMAGED = 'RETURN_DAMAGED';
    case DISPOSAL = 'DISPOSAL';
    case RMA_VENDOR = 'RMA_VENDOR';
    case OPNAME_ADJUSTMENT = 'OPNAME_ADJUSTMENT';

    public function label(): string
    {
        return match ($this) {
            self::INBOUND => 'Barang Masuk (Inbound)',
            self::OUTBOUND_TEKNISI => 'Pengeluaran Teknisi Lapangan',
            self::OUTBOUND_KARYAWAN => 'Pengeluaran Internal Karyawan',
            self::RETURN_GOOD => 'Retur Masuk - Kondisi Baik',
            self::RETURN_DAMAGED => 'Retur Masuk - Rusak / Dismantle',
            self::DISPOSAL => 'Pemusnahan / Write-Off (Scrapped)',
            self::RMA_VENDOR => 'Klaim Garansi Vendor (RMA)',
            self::OPNAME_ADJUSTMENT => 'Penyesuaian Stok Opname',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::INBOUND, self::RETURN_GOOD => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::OUTBOUND_TEKNISI => 'bg-sky-50 text-sky-700 border-sky-200',
            self::OUTBOUND_KARYAWAN => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::RETURN_DAMAGED => 'bg-orange-50 text-orange-700 border-orange-200',
            self::DISPOSAL => 'bg-rose-50 text-rose-700 border-rose-200',
            self::RMA_VENDOR => 'bg-purple-50 text-purple-700 border-purple-200',
            self::OPNAME_ADJUSTMENT => 'bg-amber-50 text-amber-700 border-amber-200',
        };
    }

    public function direction(): string
    {
        return match ($this) {
            self::INBOUND, self::RETURN_GOOD => 'in',
            self::OUTBOUND_TEKNISI, self::OUTBOUND_KARYAWAN, self::DISPOSAL, self::RMA_VENDOR => 'out',
            self::RETURN_DAMAGED, self::OPNAME_ADJUSTMENT => 'neutral',
        };
    }
}
