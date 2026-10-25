<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_order_id',
        'seller_offer_id',
        'product_id',
        'product_variant_id',
        'product_title_ar',
        'product_title_en',
        'variant_title_ar',
        'variant_title_en',
        'sku',
        'unit_price',
        'quantity',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'commission_rate',
        'commission_amount',
        'seller_net_amount',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:3',
            'quantity' => 'integer',
            'subtotal' => 'decimal:3',
            'discount_amount' => 'decimal:3',
            'tax_amount' => 'decimal:3',
            'total_amount' => 'decimal:3',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:3',
            'seller_net_amount' => 'decimal:3',
        ];
    }

    public function sellerOrder(): BelongsTo
    {
        return $this->belongsTo(SellerOrder::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(SellerOffer::class, 'seller_offer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(ProductReview::class);
    }

    public function returnItems(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function getProductTitleAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->product_title_ar ?: $this->product_title_en) : ($this->product_title_en ?: $this->product_title_ar);
    }

    public function getVariantTitleAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->variant_title_ar ?: $this->variant_title_en) : ($this->variant_title_en ?: $this->variant_title_ar);
    }
}
