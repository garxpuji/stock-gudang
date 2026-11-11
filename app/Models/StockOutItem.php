<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StockOutItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_transaction_id',
        'product_id',
        'qty',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(StockOutTransaction::class, 'stock_out_transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function serials(): BelongsToMany
    {
        return $this->belongsToMany(ProductSerial::class, 'stock_out_item_serials');
    }
}
