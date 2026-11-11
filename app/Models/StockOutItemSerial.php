<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOutItemSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_item_id',
        'product_serial_id',
    ];

    public function stockOutItem(): BelongsTo
    {
        return $this->belongsTo(StockOutItem::class);
    }

    public function productSerial(): BelongsTo
    {
        return $this->belongsTo(ProductSerial::class);
    }
}
