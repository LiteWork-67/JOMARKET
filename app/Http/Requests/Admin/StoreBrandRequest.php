<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug',
            'logo_url' => 'nullable|string|max:500',
            'website_url' => 'nullable|url|max:500',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
