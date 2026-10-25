<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isSeller();
    }

    public function rules(): array
    {
        return [
            'seller_sku' => 'required|string|max:100',
            'condition' => 'required|in:new,used,refurbished',
            'price' => 'required|numeric|min:0.001',
            'sale_price' => 'nullable|numeric|min:0.001|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'handling_days' => 'nullable|integer|min:0|max:30',
            'min_order_quantity' => 'nullable|integer|min:1',
            'max_order_quantity' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ];
    }
}
