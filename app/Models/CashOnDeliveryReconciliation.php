<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashOnDeliveryReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_order_id',
        'courier_name',
        'amount_collected',
        'status',
        'collected_at',
        'remitted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_collected' => 'decimal:3',
            'collected_at' => 'datetime',
            'remitted_at' => 'datetime',
        ];
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }
}
