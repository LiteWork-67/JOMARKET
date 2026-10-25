<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialLedgerEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'type',
        'entry_type',
        'amount',
        'currency',
        'balance_after',
        'reference_type',
        'reference_id',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:3',
            'balance_after' => 'decimal:3',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
