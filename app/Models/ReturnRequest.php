<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_order_id',
        'user_id',
        'reason',
        'status',
        'resolution_type',
        'processed_by',
        'admin_notes',
    ];

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
