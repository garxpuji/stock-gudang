<?php

namespace App\Models;

use App\Enums\DamageCategory;
use App\Enums\DamagedActionTaken;
use App\Enums\RecipientType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DamagedGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'product_id',
        'serial_number',
        'mac_address',
        'qty',
        'reported_at',
        'reported_by',
        'reporter_type',
        'damage_category',
        'damage_description',
        'photo_proof',
        'source',
        'return_item_id',
        'action_taken',
        'action_date',
        'action_notes',
        'loss_cost',
        'resolved_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:2',
            'reported_at' => 'datetime',
            'action_date' => 'datetime',
            'reporter_type' => RecipientType::class,
            'damage_category' => DamageCategory::class,
            'action_taken' => DamagedActionTaken::class,
            'loss_cost' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function returnItem(): BelongsTo
    {
        return $this->belongsTo(ReturnTransactionItem::class, 'return_item_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('action_taken', DamagedActionTaken::PENDING);
    }

    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('action_taken', '!=', DamagedActionTaken::PENDING);
    }
}
