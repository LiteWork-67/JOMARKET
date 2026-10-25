<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellerApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'store_name_ar' => 'required|string|max:255',
            'store_name_en' => 'required|string|max:255',
            'commercial_registration_no' => 'required|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:30',
            'governorate' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address_details' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string|max:2000',
            'description_en' => 'nullable|string|max:2000',
            'bank_name' => 'nullable|string|max:150',
            'iban' => 'nullable|string|max:50',
            'account_holder_name' => 'nullable|string|max:255',
        ];
    }
}
