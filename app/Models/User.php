<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'agency_id',
        'role',
        'avatar',
        'title',
        'phone',
        'last_active_at',
        'is_active',
        'is_approved',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['owner', 'admin', 'manager']);
    }

    public function hasPermissionTo($permission): bool
    {
        // Owner and admin bypass all
        if ($this->isOwner() || $this->isAdmin()) {
            return true;
        }

        return parent::hasPermissionTo($permission);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isOwner() || $this->isAdmin()) {
            return true;
        }

        return parent::hasAnyPermission($permissions);
    }

    public function canAccessAgency(Agency $agency): bool
    {
        if ($this->agency_id === $agency->id) {
            return true;
        }

        // Owner can access all agencies (if any)
        if ($this->isOwner()) {
            return true;
        }

        return false;
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            if (! $user->agency_id && ! $user->role) {
                $user->role = 'owner';
            }
        });
    }
}
