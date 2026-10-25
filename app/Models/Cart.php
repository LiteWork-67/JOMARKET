<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'currency',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return round($this->items->sum(fn ($item) => (float) $item->unit_price * $item->quantity), 3);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Group items by seller for split shipment and multi-vendor checkout.
     */
    public function getSellerGroupsAttribute()
    {
        return $this->items->groupBy(fn ($item) => $item->offer->seller_id);
    }
}
