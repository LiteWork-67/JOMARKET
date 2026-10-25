<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isSeller();
    }

    public function rules(): array
    {
        return [
            'adjustment' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:255',
        ];
    }
}
