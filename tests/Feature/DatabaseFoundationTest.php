<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\FinancialLedgerEntry;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\SellerOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_support_hierarchy_and_localization(): void
    {
        $parent = Category::factory()->create([
            'name_ar' => 'إلكترونيات',
            'name_en' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $child = Category::factory()->create([
            'parent_id' => $parent->id,
            'name_ar' => 'هواتف ذكية',
            'name_en' => 'Smartphones',
            'slug' => 'smartphones',
        ]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertCount(1, $parent->children);

        app()->setLocale('ar');
        $this->assertEquals('هواتف ذكية', $child->name);

        app()->setLocale('en');
        $this->assertEquals('Smartphones', $child->name);
    }

    public function test_seller_offers_maintain_jod_decimal_precision_and_discounts(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();

        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 199.750, // 199 JOD and 750 fils
            'sale_price' => 179.500, // 179 JOD and 500 fils
            'stock_quantity' => 10,
            'reserved_quantity' => 2,
        ]);

        $this->assertEquals(179.500, $offer->effective_price);
        $this->assertTrue($offer->hasDiscount());
        $this->assertEquals(10, $offer->discount_percentage);
        $this->assertEquals(8, $offer->available_stock);
    }

    public function test_multi_seller_order_structure_splits_and_preserves_ledger(): void
    {
        $customer = User::factory()->create();
        $sellerA = Seller::factory()->create(['commission_rate' => 10.00]);
        $sellerB = Seller::factory()->create(['commission_rate' => 15.00]);

        $parentOrder = ParentOrder::factory()->create([
            'user_id' => $customer->id,
            'subtotal_amount' => 150.000,
            'shipping_amount' => 5.000,
            'total_amount' => 155.000,
            'currency' => 'JOD',
            'payment_status' => 'paid',
        ]);

        $orderA = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $sellerA->id,
            'subtotal' => 100.000,
            'commission_rate' => 10.00,
            'commission_amount' => 10.000,
            'seller_net_amount' => 90.000,
            'shipping_fee' => 3.000,
            'total_amount' => 103.000,
        ]);

        $orderB = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $sellerB->id,
            'subtotal' => 50.000,
            'commission_rate' => 15.00,
            'commission_amount' => 7.500,
            'seller_net_amount' => 42.500,
            'shipping_fee' => 2.000,
            'total_amount' => 52.000,
        ]);

        $this->assertCount(2, $parentOrder->sellerOrders);
        $this->assertEquals(90.000, (float) $orderA->seller_net_amount);
        $this->assertEquals(42.500, (float) $orderB->seller_net_amount);

        // Record seller net credits into ledger
        FinancialLedgerEntry::create([
            'seller_id' => $sellerA->id,
            'type' => 'sale',
            'entry_type' => 'credit',
            'amount' => 90.000,
            'currency' => 'JOD',
            'balance_after' => 90.000,
            'reference_type' => SellerOrder::class,
            'reference_id' => $orderA->id,
            'description' => 'Sale credit for order '.$orderA->order_number,
        ]);

        $this->assertEquals(90.000, $sellerA->getSettledBalance());
    }

    public function test_coupon_discount_calculation_and_spend_limits(): void
    {
        $coupon = Coupon::create([
            'code' => 'AMMAN15',
            'type' => 'percentage',
            'value' => 15.000, // 15%
            'min_order_amount' => 30.000,
            'max_discount_amount' => 10.000,
            'usage_limit' => 50,
            'usage_count' => 0,
            'is_active' => true,
        ]);

        // Below minimum
        $this->assertFalse($coupon->isValidForAmount(20.000));
        $this->assertEquals(0.000, $coupon->calculateDiscount(20.000));

        // Valid, capped at max discount of 10.000 JOD (15% of 100 = 15, capped to 10)
        $this->assertTrue($coupon->isValidForAmount(100.000));
        $this->assertEquals(10.000, $coupon->calculateDiscount(100.000));

        // Valid, normal percentage (15% of 40 = 6 JOD)
        $this->assertEquals(6.000, $coupon->calculateDiscount(40.000));
    }
}
