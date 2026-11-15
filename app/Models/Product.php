<?php

namespace App\Models;

use App\Enums\StockStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'category_id',
        'unit_id',
        'location_id',
        'stock',
        'damaged_stock',
        'min_stock',
        'cost_price',
        'has_serial_number',
        'specification',
        'stock_notes',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'decimal:2',
            'damaged_stock' => 'decimal:2',
            'min_stock' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'has_serial_number' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function serials(): HasMany
    {
        return $this->hasMany(ProductSerial::class);
    }

    public function availableSerials(): HasMany
    {
        return $this->hasMany(ProductSerial::class)->where('status', 'AVAILABLE');
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(StockInItem::class);
    }

    public function stockOutItems(): HasMany
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnTransactionItem::class);
    }

    public function damagedGoods(): HasMany
    {
        return $this->hasMany(DamagedGood::class);
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(StockMutation::class)->latest();
    }

    /**
     * Compute real-time ISP stock status
     */
    public function getStockStatusAttribute(): StockStatus
    {
        $stock = (float) $this->stock;
        $min = (float) $this->min_stock;

        if ($stock <= 0) {
            return StockStatus::EMPTY;
        }

        if ($stock <= $min) {
            return StockStatus::LOW;
        }

        return StockStatus::SAFE;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->stock_status->label();
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->stock_status->badge();
    }

    public function getHasDamagedAttribute(): bool
    {
        return (float) $this->damaged_stock > 0;
    }

    public function getFormattedStockAttribute(): string
    {
        $val = (float) $this->stock;
        $formatted = (floor($val) == $val) ? number_format($val, 0) : number_format($val, 2);

        return $formatted.' '.($this->unit?->abbreviation ?? '');
    }

    public function getFormattedDamagedStockAttribute(): string
    {
        $val = (float) $this->damaged_stock;
        $formatted = (floor($val) == $val) ? number_format($val, 0) : number_format($val, 2);

        return $formatted.' '.($this->unit?->abbreviation ?? '');
    }

    // Scopes
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock', '<=', 'min_stock')->where('stock', '>', 0);
    }

    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('stock', '<=', 0);
    }

    public function scopeHasDamaged(Builder $query): Builder
    {
        return $query->where('damaged_stock', '>', 0);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%")
                ->orWhereHas('serials', function ($sq) use ($term) {
                    $sq->where('serial_number', 'like', "%{$term}%")
                        ->orWhere('mac_address', 'like', "%{$term}%");
                });
        });
    }
}
