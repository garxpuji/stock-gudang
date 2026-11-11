<?php

namespace App\Models;

use App\Enums\SerialStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductSerial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'serial_number',
        'mac_address',
        'status',
        'last_recipient',
        'last_ticket_no',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SerialStatus::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockOutItems(): BelongsToMany
    {
        return $this->belongsToMany(StockOutItem::class, 'stock_out_item_serials');
    }
}
