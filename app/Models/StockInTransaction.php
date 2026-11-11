<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockInTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'supplier_id',
        'supplier_name',
        'po_number',
        'vendor_delivery_order_no',
        'courier_or_driver_name',
        'transaction_date',
        'received_by_user_id',
        'notes',
        'total_items',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'total_items' => 'integer',
            'total_cost' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }
}
