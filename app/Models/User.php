<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'pin', 'phone', 'avatar_url', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token', 'pin',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function shifts(): HasMany {
        return $this->hasMany(PosShift::class);
    }

    public function transactions(): HasMany {
        return $this->hasMany(PosTransaction::class);
    }

    public function currentShift(): ?PosShift {
        return $this->shifts()->where('status', 'open')->latest('opened_at')->first();
    }

    public function isAdmin(): bool { return in_array($this->role, ['admin', 'superadministrator']); }
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isCashier(): bool { return $this->role === 'cashier'; }
    public function canManageInventory(): bool { return in_array($this->role, ['admin','manager','superadministrator'], true); }
}
