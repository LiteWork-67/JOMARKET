<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SellerOffer;
use App\Models\SellerOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(3, 5, 50);
        $qty = fake()->numberBetween(1, 3);
        $subtotal = round($unitPrice * $qty, 3);
        $commRate = 10.00;
        $commAmount = round(($subtotal * $commRate) / 100, 3);
        $net = round($subtotal - $commAmount, 3);

        return [
            'seller_order_id' => SellerOrder::factory(),
            'seller_offer_id' => SellerOffer::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'product_title_ar' => 'هاتف ذكي فائق السرعة',
            'product_title_en' => 'Ultra High Speed Smartphone',
            'variant_title_ar' => null,
            'variant_title_en' => null,
            'sku' => 'SKU-'.fake()->numerify('#####'),
            'unit_price' => $unitPrice,
            'quantity' => $qty,
            'subtotal' => $subtotal,
            'discount_amount' => 0.000,
            'tax_amount' => 0.000,
            'total_amount' => $subtotal,
            'commission_rate' => $commRate,
            'commission_amount' => $commAmount,
            'seller_net_amount' => $net,
        ];
    }
}
