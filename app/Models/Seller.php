<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name_ar',
        'store_name_en',
        'slug',
        'logo_url',
        'banner_url',
        'description_ar',
        'description_en',
        'commission_rate',
        'status',
        'rejection_reason',
        'commercial_registration_no',
        'tax_number',
        'bank_name',
        'iban',
        'account_holder_name',
        'contact_email',
        'contact_phone',
        'governorate',
        'city',
        'address_details',
        'rating',
        'reviews_count',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'rating' => 'decimal:2',
            'reviews_count' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(SellerStaff::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SellerDocument::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(SellerOffer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(SellerOrder::class);
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(SellerPayoutRequest::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(FinancialLedgerEntry::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getSettledBalance(): float
    {
        $credits = (float) $this->ledgerEntries()->where('entry_type', 'credit')->sum('amount');
        $debits = (float) $this->ledgerEntries()->where('entry_type', 'debit')->sum('amount');

        return round($credits - $debits, 3);
    }

    public function getStoreNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->store_name_ar ?: $this->store_name_en) : ($this->store_name_en ?: $this->store_name_ar);
    }
}
