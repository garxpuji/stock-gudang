<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockInTransaction::class, 'received_by_user_id');
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOutTransaction::class, 'dispatched_by_user_id');
    }

    public function returns(): HasMany
    {
        return $this->hasMany(ReturnTransaction::class, 'received_by_user_id');
    }

    public function resolvedDamagedGoods(): HasMany
    {
        return $this->hasMany(DamagedGood::class, 'resolved_by_user_id');
    }

    public function mutations(): HasMany
    {
        return $this->hasMany(StockMutation::class, 'system_operator_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdminGudang(): bool
    {
        return in_array($this->role, ['admin_gudang', 'superadmin'], true);
    }

    public function isStaffGudang(): bool
    {
        return $this->role === 'staff_gudang';
    }

    public function getRoleBadgeAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'bg-purple-50 text-purple-700 border-purple-200',
            'admin_gudang' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'staff_gudang' => 'bg-sky-50 text-sky-700 border-sky-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
