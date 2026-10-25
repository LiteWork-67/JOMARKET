<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_offer_id',
        'cart_id',
        'checkout_token',
        'quantity',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'expires_at' => 'datetime',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(SellerOffer::class, 'seller_offer_id');
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
