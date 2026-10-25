<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SellerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_order_id',
        'seller_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_fee',
        'commission_rate',
        'commission_amount',
        'seller_net_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'shipping_method',
        'courier_name',
        'tracking_number',
        'shipped_at',
        'delivered_at',
        'cancellation_reason',
        'cancelled_by',
        'seller_notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:3',
            'shipping_fee' => 'decimal:3',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:3',
            'seller_net_amount' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'tax_amount' => 'decimal:3',
            'total_amount' => 'decimal:3',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(ParentOrder::class, 'parent_order_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function cancellation(): HasOne
    {
        return $this->hasOne(Cancellation::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function codReconciliation(): HasOne
    {
        return $this->hasOne(CashOnDeliveryReconciliation::class);
    }

    public function cancelledByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending_confirmation', 'confirmed', 'processing'], true);
    }

    public function canBeReturned(): bool
    {
        return $this->status === 'delivered';
    }
}
