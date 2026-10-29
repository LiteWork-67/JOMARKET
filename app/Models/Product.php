<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'short_description_ar',
        'short_description_en',
        'main_image_url',
        'barcode',
        'sku_prefix',
        'status',
        'rejection_reason',
        'is_featured',
        'is_active',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(SellerOffer::class);
    }

    public function activeOffers(): HasMany
    {
        return $this->hasMany(SellerOffer::class)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('price');
    }

    public function bestOffer(): HasOne
    {
        return $this->hasOne(SellerOffer::class)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('price');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('status', 'approved');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function getTitleArAttribute(): string
    {
        return $this->name_ar;
    }

    public function getTitleEnAttribute(): string
    {
        return $this->name_en;
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        return $this->main_image_url;
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->description_ar ?: $this->description_en) : ($this->description_en ?: $this->description_ar);
    }

    public function getShortDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->short_description_ar ?: $this->short_description_en) : ($this->short_description_en ?: $this->short_description_ar);
    }
}
