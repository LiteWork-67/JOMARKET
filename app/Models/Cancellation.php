<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_order_id',
        'requested_by',
        'reason',
        'status',
        'processed_by',
        'admin_notes',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'processed_at' => 'datetime',
        ];
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
