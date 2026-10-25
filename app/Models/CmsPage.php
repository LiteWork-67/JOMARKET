<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->title_ar ?: $this->title_en) : ($this->title_en ?: $this->title_ar);
    }

    public function getContentAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'ar' ? ($this->content_ar ?: $this->content_en) : ($this->content_en ?: $this->content_ar);
    }
}
