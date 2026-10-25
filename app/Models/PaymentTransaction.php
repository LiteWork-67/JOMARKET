<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_order_id',
        'amount',
        'currency',
        'provider',
        'transaction_id',
        'status',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'payload' => 'array',
        ];
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(ParentOrder::class);
    }
}
