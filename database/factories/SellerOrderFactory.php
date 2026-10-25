<?php

namespace Database\Factories;

use App\Models\ParentOrder;
use App\Models\Seller;
use App\Models\SellerOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerOrder>
 */
class SellerOrderFactory extends Factory
{
    protected $model = SellerOrder::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(3, 10, 150);
        $shipping = 2.500;
        $commissionRate = 10.00;
        $commission = round(($subtotal * $commissionRate) / 100, 3);
        $sellerNet = round($subtotal - $commission, 3);
        $total = $subtotal + $shipping;

        return [
            'parent_order_id' => ParentOrder::factory(),
            'seller_id' => Seller::factory(),
            'order_number' => 'SO-'.strtoupper(fake()->unique()->bothify('##??####')),
            'status' => 'pending_confirmation',
            'subtotal' => $subtotal,
            'shipping_fee' => $shipping,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commission,
            'seller_net_amount' => $sellerNet,
            'discount_amount' => 0.000,
            'tax_amount' => 0.000,
            'total_amount' => $total,
            'shipping_method' => 'standard',
            'courier_name' => 'Jordan Post / Aramex',
            'tracking_number' => null,
            'seller_notes' => null,
        ];
    }
}
