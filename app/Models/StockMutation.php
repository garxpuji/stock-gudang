<?php

namespace App\Models;

use App\Enums\MutationType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'mutation_type',
        'qty',
        'previous_stock',
        'current_stock',
        'previous_damaged_stock',
        'current_damaged_stock',
        'system_operator_id',
        'person_in_charge',
        'reference_table',
        'reference_id',
        'reference_ticket_no',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'mutation_type' => MutationType::class,
            'qty' => 'decimal:2',
            'previous_stock' => 'decimal:2',
            'current_stock' => 'decimal:2',
            'previous_damaged_stock' => 'decimal:2',
            'current_damaged_stock' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function systemOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'system_operator_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (! empty($filters['mutation_type'])) {
            $query->where('mutation_type', $filters['mutation_type']);
        }

        if (! empty($filters['ticket_no'])) {
            $query->where('reference_ticket_no', 'like', "%{$filters['ticket_no']}%");
        }

        if (! empty($filters['person'])) {
            $query->where('person_in_charge', 'like', "%{$filters['person']}%");
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('person_in_charge', 'like', "%{$search}%")
                    ->orWhere('reference_ticket_no', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }
}
