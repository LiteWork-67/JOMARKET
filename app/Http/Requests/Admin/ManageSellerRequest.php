<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ManageSellerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'action' => 'required|in:approve,reject,suspend,reactivate',
            'rejection_reason' => 'nullable|required_if:action,reject|string|max:500',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
        ];
    }
}
