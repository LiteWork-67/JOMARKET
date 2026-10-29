<?php

namespace Tests\Feature;

use App\Livewire\CartPage;
use App\Livewire\Checkout;
use App\Models\Coupon;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_cart_page(): void
    {
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);
        $response->assertSee(__('messages.shopping_cart'));
    }

    public function test_can_add_item_to_cart_and_update_quantity(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create(['name_ar' => 'سماعات بلوتوث لاسلكية']);
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 20.000,
            'stock_quantity' => 10,
        ]);

        $cartService = app(CartService::class);
        $cartItem = $cartService->addItem($offer->id, 1);

        $this->assertEquals(1, $cartItem->quantity);

        // Increment via Livewire component
        Livewire::test(CartPage::class)
            ->call('incrementQuantity', $cartItem->id)
            ->assertDispatched('cart-updated');

        $this->assertEquals(2, $cartItem->fresh()->quantity);

        // Decrement via Livewire component
        Livewire::test(CartPage::class)
            ->call('decrementQuantity', $cartItem->id)
            ->assertDispatched('cart-updated');

        $this->assertEquals(1, $cartItem->fresh()->quantity);
    }

    public function test_can_remove_item_from_cart(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'stock_quantity' => 10,
        ]);

        $cartService = app(CartService::class);
        $cartItem = $cartService->addItem($offer->id, 1);

        Livewire::test(CartPage::class)
            ->call('removeItem', $cartItem->id)
            ->assertDispatched('cart-updated');

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_can_apply_and_remove_coupon(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 50.000,
            'stock_quantity' => 5,
        ]);

        $coupon = Coupon::create([
            'code' => 'JORDAN10',
            'type' => 'percentage',
            'value' => 10.000,
            'min_order_amount' => 30.000,
            'is_active' => true,
        ]);

        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 1);

        Livewire::test(CartPage::class)
            ->set('couponCode', 'JORDAN10')
            ->call('applyCoupon')
            ->assertSet('couponMessage', __('messages.coupon_applied'));

        $totals = $cartService->getTotals();
        $this->assertEquals(5.000, $totals['discount']); // 10% of 50 = 5

        // Remove coupon
        Livewire::test(CartPage::class)
            ->call('removeCoupon');

        $this->assertEquals(0.000, $cartService->getTotals()['discount']);
    }

    public function test_invalid_coupon_shows_error(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 10.000,
            'stock_quantity' => 5,
        ]);

        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 1);

        Livewire::test(CartPage::class)
            ->set('couponCode', 'NON_EXISTENT_CODE')
            ->call('applyCoupon')
            ->assertSet('couponError', __('messages.invalid_coupon'));
    }

    public function test_free_shipping_on_orders_above_50_jod(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 60.000,
            'stock_quantity' => 5,
        ]);

        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 1);

        $totals = $cartService->getTotals('عمّان');
        $this->assertEquals(0.000, $totals['shipping']);
    }

    public function test_standard_shipping_under_50_jod(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 20.000,
            'stock_quantity' => 5,
        ]);

        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 1);

        $ammanTotals = $cartService->getTotals('عمّان');
        $this->assertEquals(2.000, $ammanTotals['shipping']);

        $irbidTotals = $cartService->getTotals('إربد');
        $this->assertEquals(3.000, $irbidTotals['shipping']);
    }

    public function test_atomic_checkout_creates_parent_order_and_splits_seller_orders(): void
    {
        $seller1 = Seller::factory()->create(['commission_rate' => 10.00]);
        $seller2 = Seller::factory()->create(['commission_rate' => 15.00]);

        $product1 = Product::factory()->create(['name_ar' => 'منتج بائع 1']);
        $product2 = Product::factory()->create(['name_ar' => 'منتج بائع 2']);

        $offer1 = SellerOffer::factory()->create([
            'seller_id' => $seller1->id,
            'product_id' => $product1->id,
            'price' => 25.000,
            'stock_quantity' => 10,
        ]);

        $offer2 = SellerOffer::factory()->create([
            'seller_id' => $seller2->id,
            'product_id' => $product2->id,
            'price' => 30.000,
            'stock_quantity' => 8,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $cartService = app(CartService::class);
        $cartService->addItem($offer1->id, 2); // 50.000 JOD
        $cartService->addItem($offer2->id, 1); // 30.000 JOD

        $checkoutService = app(CheckoutService::class);

        $order = $checkoutService->processCheckout($customer, [
            'contact_name' => 'سامر الكردي',
            'phone' => '0795554433',
            'governorate' => 'عمّان',
            'city' => 'الشميساني',
            'street_address' => 'شارع الثقافة',
            'payment_method' => 'cash_on_delivery',
        ]);

        // Verify Parent Order
        $this->assertDatabaseHas('parent_orders', [
            'id' => $order->id,
            'user_id' => $customer->id,
            'subtotal_amount' => 80.000,
            'shipping_amount' => 0.000, // >= 50 JOD free shipping!
            'total_amount' => 80.000,
            'status' => 'pending',
            'payment_method' => 'cash_on_delivery',
        ]);

        // Verify 2 split Seller Orders
        $this->assertCount(2, $order->sellerOrders);
        $this->assertDatabaseHas('seller_orders', [
            'parent_order_id' => $order->id,
            'seller_id' => $seller1->id,
            'subtotal' => 50.000,
            'commission_rate' => 10.00,
            'commission_amount' => 5.000,
            'seller_net_amount' => 45.000,
        ]);
        $this->assertDatabaseHas('seller_orders', [
            'parent_order_id' => $order->id,
            'seller_id' => $seller2->id,
            'subtotal' => 30.000,
            'commission_rate' => 15.00,
            'commission_amount' => 4.500,
            'seller_net_amount' => 25.500,
        ]);

        // Verify Stock Deductions & Inventory Movements
        $this->assertEquals(8, $offer1->fresh()->available_stock); // 10 - 2
        $this->assertEquals(7, $offer2->fresh()->available_stock); // 8 - 1

        $this->assertDatabaseHas('inventory_movements', [
            'seller_offer_id' => $offer1->id,
            'type' => 'sale',
            'quantity' => -2,
            'balance_after' => 8,
        ]);

        // Verify Payment Transaction
        $this->assertDatabaseHas('payment_transactions', [
            'parent_order_id' => $order->id,
            'amount' => 80.000,
            'provider' => 'cod',
            'status' => 'initiated',
        ]);
    }

    public function test_checkout_via_livewire_component(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 15.000,
            'stock_quantity' => 10,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 1);

        Livewire::test(Checkout::class)
            ->set('contactName', 'حمزة المجالي')
            ->set('phone', '0788887766')
            ->set('governorate', 'الكرك')
            ->set('city', 'الكرك القديمة')
            ->set('streetAddress', 'شارع القلعة')
            ->set('paymentMethod', 'cliq')
            ->set('cliqAlias', 'HamzaCliQ')
            ->call('placeOrder')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('parent_orders', [
            'user_id' => $customer->id,
            'payment_method' => 'bank_transfer',
        ]);
    }

    public function test_can_view_order_confirmation_page(): void
    {
        $customer = User::factory()->create();
        $parentOrder = ParentOrder::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'JM-20261029-TEST1',
        ]);

        $response = $this->get(route('orders.confirmation', 'JM-20261029-TEST1'));

        $response->assertStatus(200);
        $response->assertSee('JM-20261029-TEST1');
        $response->assertSee(__('messages.order_confirmed_title'));
    }
}
