<?php

namespace Tests\Feature;

use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\SellerOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sellers_cannot_access_or_mix_other_sellers_orders_and_offers(): void
    {
        $sellerOne = Seller::factory()->create(['store_name_en' => 'Store One']);
        $sellerTwo = Seller::factory()->create(['store_name_en' => 'Store Two']);

        $product = Product::factory()->create();

        $offerOne = SellerOffer::factory()->create([
            'seller_id' => $sellerOne->id,
            'product_id' => $product->id,
            'price' => 50.000,
        ]);

        $offerTwo = SellerOffer::factory()->create([
            'seller_id' => $sellerTwo->id,
            'product_id' => $product->id,
            'price' => 55.000,
        ]);

        // Seller One offers
        $this->assertTrue($sellerOne->offers->contains($offerOne));
        $this->assertFalse($sellerOne->offers->contains($offerTwo));

        // Create orders for both
        $parentOrder = ParentOrder::factory()->create();

        $orderOne = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $sellerOne->id,
        ]);

        $orderTwo = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $sellerTwo->id,
        ]);

        $this->assertTrue($sellerOne->orders->contains($orderOne));
        $this->assertFalse($sellerOne->orders->contains($orderTwo));

        $this->assertTrue($sellerTwo->orders->contains($orderTwo));
        $this->assertFalse($sellerTwo->orders->contains($orderOne));
    }
}
