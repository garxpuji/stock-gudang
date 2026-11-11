<?php

namespace App\Models;

use App\Enums\RecipientType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'returned_by',
        'returned_by_contact',
        'recipient_type',
        'original_stock_out_id',
        'reference_ticket_no',
        'return_category',
        'keterangan_pengembalian',
        'received_by_user_id',
        'transaction_date',
        'total_items',
    ];

    protected function casts(): array
    {
        return [
            'recipient_type' => RecipientType::class,
            'transaction_date' => 'datetime',
            'total_items' => 'integer',
        ];
    }

    public function originalStockOut(): BelongsTo
    {
        return $this->belongsTo(StockOutTransaction::class, 'original_stock_out_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnTransactionItem::class);
    }
}
