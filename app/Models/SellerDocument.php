<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'document_type',
        'file_path',
        'original_filename',
        'file_size',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
