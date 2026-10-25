<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'attribute_id',
        'value_ar',
        'value_en',
        'hex_color',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function getValueAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->value_ar ?: $this->value_en) : ($this->value_en ?: $this->value_ar);
    }
}
