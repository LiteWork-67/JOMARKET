<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isSeller() || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:confirmed,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|required_if:status,shipped|string|max:100',
            'courier_name' => 'nullable|string|max:100',
            'seller_notes' => 'nullable|string|max:500',
        ];
    }
}
