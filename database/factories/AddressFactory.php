<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        $governorates = ['Amman', 'Zarqa', 'Irbid', 'Balqa', 'Aqaba', 'Madaba', 'Jerash'];
        $cities = ['Amman', 'Sweileh', 'Wadi Al-Seer', 'Zarqa', 'Irbid', 'Aqaba', 'Salt'];

        return [
            'user_id' => User::factory(),
            'type' => 'shipping',
            'contact_name' => fake()->name(),
            'phone' => '+9627'.fake()->numerify('########'),
            'governorate' => fake()->randomElement($governorates),
            'city' => fake()->randomElement($cities),
            'district' => fake()->streetName(),
            'street_address' => fake()->streetAddress(),
            'building_number' => (string) fake()->numberBetween(1, 150),
            'floor' => (string) fake()->numberBetween(1, 6),
            'apartment' => (string) fake()->numberBetween(1, 20),
            'landmark' => 'Near '.fake()->company(),
            'is_default' => false,
            'latitude' => fake()->latitude(31.5, 32.5),
            'longitude' => fake()->longitude(35.5, 36.5),
        ];
    }
}
