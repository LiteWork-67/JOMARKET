<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal_amount',
        'shipping_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'payment_status',
        'payment_method',
        'payment_reference',
        'shipping_address_snapshot',
        'billing_address_snapshot',
        'customer_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'decimal:3',
            'shipping_amount' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'tax_amount' => 'decimal:3',
            'total_amount' => 'decimal:3',
            'shipping_address_snapshot' => 'array',
            'billing_address_snapshot' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sellerOrders(): HasMany
    {
        return $this->hasMany(SellerOrder::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
