<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'locale',
        'last_login_at',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultShippingAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function seller(): HasOne
    {
        return $this->hasOne(Seller::class);
    }

    public function sellerStaff(): HasMany
    {
        return $this->hasMany(SellerStaff::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function activeCart(): HasOne
    {
        return $this->hasOne(Cart::class)->where('status', 'active')->latestOfMany();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ParentOrder::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            if (in_array($this->role, $role, true)) {
                return true;
            }

            return $this->roles->pluck('slug')->intersect($role)->isNotEmpty();
        }

        if ($this->role === $role) {
            return true;
        }

        return $this->roles->contains('slug', $role);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn ($role) => $role->permissions)
            ->contains('slug', $permissionSlug);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSeller(): bool
    {
        return $this->hasRole('seller') || $this->seller()->exists();
    }

    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
