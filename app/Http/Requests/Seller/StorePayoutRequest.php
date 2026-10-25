<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StorePayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isSeller();
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
