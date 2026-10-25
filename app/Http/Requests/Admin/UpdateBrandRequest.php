<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $brandId = $this->route('brand') ?? $this->route('id');

        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('brands', 'slug')->ignore($brandId)],
            'logo_url' => 'nullable|string|max:500',
            'website_url' => 'nullable|url|max:500',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ];
    }
}
