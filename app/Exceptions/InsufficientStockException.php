<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $productName, float $requested, float $available)
    {
        parent::__construct("Stok tidak mencukupi untuk '{$productName}'. Diminta: {$requested}, Tersedia di gudang: {$available}.");
    }
}
