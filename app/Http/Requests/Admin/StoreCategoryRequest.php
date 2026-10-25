<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'slug' => 'required|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description_ar' => 'nullable|string|max:1000',
            'description_en' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ];
    }
}
