<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ModerateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'nullable|required_if:action,reject|string|max:500',
        ];
    }
}
