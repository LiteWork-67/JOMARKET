<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerPayoutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'amount',
        'currency',
        'status',
        'requested_by',
        'approved_by',
        'rejection_reason',
        'transaction_reference',
        'notes',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'processed_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
