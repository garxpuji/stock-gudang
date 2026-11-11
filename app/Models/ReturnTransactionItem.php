<?php

namespace App\Models;

use App\Enums\QcStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReturnTransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_transaction_id',
        'product_id',
        'qty',
        'qc_status',
        'serial_number',
        'mac_address',
        'qc_notes',
        'action_taken',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'qc_status' => QcStatus::class,
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(ReturnTransaction::class, 'return_transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function damagedGoodRecord(): HasOne
    {
        return $this->hasOne(DamagedGood::class, 'return_item_id');
    }
}
