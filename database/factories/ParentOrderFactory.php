<?php

namespace Database\Factories;

use App\Models\ParentOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ParentOrder>
 */
class ParentOrderFactory extends Factory
{
    protected $model = ParentOrder::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(3, 20, 300);
        $shipping = 3.000;
        $total = $subtotal + $shipping;

        return [
            'order_number' => 'JM-'.strtoupper(fake()->unique()->bothify('##??####')),
            'user_id' => User::factory(),
            'status' => 'pending',
            'subtotal_amount' => $subtotal,
            'shipping_amount' => $shipping,
            'discount_amount' => 0.000,
            'tax_amount' => 0.000,
            'total_amount' => $total,
            'currency' => 'JOD',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'payment_reference' => null,
            'shipping_address_snapshot' => [
                'contact_name' => fake()->name(),
                'phone' => '+962791234567',
                'governorate' => 'Amman',
                'city' => 'Amman',
                'street_address' => 'Medina St',
            ],
            'billing_address_snapshot' => null,
            'customer_notes' => null,
        ];
    }
}
