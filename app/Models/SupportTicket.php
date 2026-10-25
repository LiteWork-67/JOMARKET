<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'seller_id',
        'parent_order_id',
        'subject',
        'priority',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(ParentOrder::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class)->orderBy('created_at');
    }
}
