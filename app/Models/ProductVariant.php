<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'title_ar',
        'title_en',
        'weight_kg',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:3',
            'is_active' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductAttributeValue::class,
            'product_variant_attribute_value',
            'product_variant_id',
            'attribute_value_id'
        );
    }

    public function offers(): HasMany
    {
        return $this->hasMany(SellerOffer::class);
    }

    public function getTitleAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->title_ar ?: $this->title_en) : ($this->title_en ?: $this->title_ar);
    }
}
