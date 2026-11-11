<?php

namespace App\Models;

use App\Enums\RecipientType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOutTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'recipient_type',
        'picked_up_by',
        'recipient_department_or_team',
        'recipient_phone',
        'reference_ticket_no',
        'purpose',
        'keterangan_pengeluaran',
        'dispatched_by_user_id',
        'transaction_date',
        'status',
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

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnTransaction::class, 'original_stock_out_id');
    }
}
