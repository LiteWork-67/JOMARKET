<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isSeller() || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'store_name_ar' => 'required|string|max:255',
            'store_name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:2000',
            'description_en' => 'nullable|string|max:2000',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:30',
            'bank_name' => 'nullable|string|max:150',
            'iban' => 'nullable|string|max:50',
            'account_holder_name' => 'nullable|string|max:255',
            'governorate' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address_details' => 'nullable|string|max:500',
        ];
    }
}
