<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'image_url',
        'icon',
        'commission_rate',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar);
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->description_ar ?: $this->description_en) : ($this->description_en ?: $this->description_ar);
    }
}
