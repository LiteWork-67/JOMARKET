<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_order_id',
        'seller_order_id',
        'amount',
        'currency',
        'status',
        'refund_method',
        'transaction_reference',
        'reason',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
        ];
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(ParentOrder::class);
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
