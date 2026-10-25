<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerOffer>
 */
class SellerOfferFactory extends Factory
{
    protected $model = SellerOffer::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(3, 5, 250);

        return [
            'seller_id' => Seller::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => null,
            'seller_sku' => 'SKU-'.fake()->unique()->numerify('#####'),
            'condition' => 'new',
            'price' => $price,
            'sale_price' => null,
            'cost_price' => round($price * 0.7, 3),
            'currency' => 'JOD',
            'stock_quantity' => fake()->numberBetween(10, 100),
            'reserved_quantity' => 0,
            'min_order_quantity' => 1,
            'max_order_quantity' => 10,
            'handling_days' => 1,
            'is_active' => true,
            'is_featured' => false,
        ];
    }
}
