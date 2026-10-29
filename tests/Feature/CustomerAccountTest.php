<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\User;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_account_pages(): void
    {
        $response = $this->get(route('account.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_can_view_account_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get(route('account.dashboard'));

        $response->assertStatus(200);
        $response->assertSee(__('messages.account_overview'));
    }

    public function test_customer_can_manage_addresses(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        // 1. Add Address
        $response = $this->actingAs($customer)->post(route('account.addresses.store'), [
            'contact_name' => 'يزن التميمي',
            'phone' => '0791112233',
            'governorate' => 'عمّان',
            'city' => 'تلاع العلي',
            'street_address' => 'شارع المدينة المنورة',
            'building_number' => '12',
            'is_default' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('addresses', [
            'user_id' => $customer->id,
            'contact_name' => 'يزن التميمي',
            'city' => 'تلاع العلي',
            'is_default' => true,
        ]);

        $address = Address::where('user_id', $customer->id)->first();

        // 2. Add Second Address and set as default
        $response2 = $this->actingAs($customer)->post(route('account.addresses.store'), [
            'contact_name' => 'يزن التميمي - العمل',
            'phone' => '0791112233',
            'governorate' => 'الزرقاء',
            'city' => 'الزرقاء الجديدة',
            'street_address' => 'شارع 36',
            'is_default' => true,
        ]);

        $response2->assertRedirect();
        $this->assertFalse($address->fresh()->is_default); // First address was unset

        // 3. Delete Address
        $this->actingAs($customer)->delete(route('account.addresses.delete', $address->id));
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    public function test_customer_can_view_orders_and_order_detail(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = ParentOrder::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'JM-20261029-ABC12',
            'status' => 'pending',
            'total_amount' => 35.000,
        ]);

        // List orders
        $response = $this->actingAs($customer)->get(route('account.orders'));
        $response->assertStatus(200);
        $response->assertSee('JM-20261029-ABC12');

        // Order detail
        $detailResponse = $this->actingAs($customer)->get(route('account.orders.show', 'JM-20261029-ABC12'));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('JM-20261029-ABC12');
    }

    public function test_customer_can_cancel_pending_order_and_inventory_is_restored(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 20.000,
            'stock_quantity' => 10,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer);

        // Place order
        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 3); // Deducts stock from 10 to 7

        $checkoutService = app(CheckoutService::class);
        $order = $checkoutService->processCheckout($customer, [
            'contact_name' => 'خالد الحنيطي',
            'phone' => '0787776655',
            'governorate' => 'عمّان',
            'city' => 'أبو علندا',
            'street_address' => 'الشارع العام',
            'payment_method' => 'cash_on_delivery',
        ]);

        $this->assertEquals(7, $offer->fresh()->available_stock);

        // Customer cancels order
        $cancelResponse = $this->actingAs($customer)->post(route('account.orders.cancel', $order->order_number), [
            'reason' => 'Ordered by mistake',
        ]);

        $cancelResponse->assertRedirect();
        $this->assertDatabaseHas('parent_orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);

        // Stock restored back to 10
        $this->assertEquals(10, $offer->fresh()->available_stock);

        // Inventory Movement created
        $this->assertDatabaseHas('inventory_movements', [
            'seller_offer_id' => $offer->id,
            'type' => 'release',
            'quantity' => 3,
            'balance_after' => 10,
        ]);
    }

    public function test_customer_cannot_cancel_processing_or_shipped_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = ParentOrder::factory()->create([
            'user_id' => $customer->id,
            'order_number' => 'JM-20261029-PROC1',
            'status' => 'processing',
        ]);

        $response = $this->actingAs($customer)->post(route('account.orders.cancel', $order->order_number), [
            'reason' => 'Changed mind',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('processing', $order->fresh()->status);
    }

    public function test_customer_can_update_profile_and_password(): void
    {
        $customer = User::factory()->create([
            'name' => 'الاسم القديم',
            'phone' => '0790001122',
            'password' => Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($customer)->put(route('account.profile.update'), [
            'name' => 'الاسم الجديد المحدث',
            'phone' => '0793334455',
            'current_password' => 'OldPassword123!',
            'new_password' => 'NewPassword123!',
            'new_password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect();
        $customer->refresh();

        $this->assertEquals('الاسم الجديد المحدث', $customer->name);
        $this->assertEquals('0793334455', $customer->phone);
        $this->assertTrue(Hash::check('NewPassword123!', $customer->password));
    }

    public function test_customer_can_toggle_wishlist(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $product = Product::factory()->create();

        // 1. Add to wishlist
        $this->actingAs($customer)->post(route('account.wishlist.toggle', $product->id));
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        // 2. Remove from wishlist
        $this->actingAs($customer)->post(route('account.wishlist.toggle', $product->id));
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);
    }
}
