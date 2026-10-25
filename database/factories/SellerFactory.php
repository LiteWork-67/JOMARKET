<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        $nameEn = fake()->company();
        $nameAr = 'متجر '.fake()->company();

        return [
            'user_id' => User::factory()->seller(),
            'store_name_ar' => $nameAr,
            'store_name_en' => $nameEn,
            'slug' => Str::slug($nameEn).'-'.fake()->unique()->numberBetween(100, 9999),
            'logo_url' => null,
            'banner_url' => null,
            'description_ar' => fake()->paragraph(),
            'description_en' => fake()->paragraph(),
            'commission_rate' => 10.00,
            'status' => 'approved',
            'commercial_registration_no' => 'CR-'.fake()->numerify('######'),
            'tax_number' => 'TAX-'.fake()->numerify('########'),
            'bank_name' => 'Arab Bank',
            'iban' => 'JO94ARAB000000'.fake()->numerify('#############'),
            'account_holder_name' => $nameEn,
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => '+9627'.fake()->numerify('########'),
            'governorate' => 'Amman',
            'city' => 'Amman',
            'address_details' => 'King Abdullah II St, Amman',
            'rating' => 4.80,
            'reviews_count' => 15,
            'verified_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'verified_at' => null,
        ]);
    }
}
