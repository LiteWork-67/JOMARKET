<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:3',
            'min_order_amount' => 'decimal:3',
            'max_discount_amount' => 'decimal:3',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    public function isValidForAmount(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit > 0 && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (! $this->isValidForAmount($subtotal)) {
            return 0.000;
        }

        if ($this->type === 'percentage') {
            $discount = ($subtotal * (float) $this->value) / 100;
            if ($this->max_discount_amount && $discount > (float) $this->max_discount_amount) {
                $discount = (float) $this->max_discount_amount;
            }

            return round($discount, 3);
        }

        return min(round((float) $this->value, 3), $subtotal);
    }
}
