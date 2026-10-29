<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee(__('messages.login_title'));
    }

    public function test_customer_can_login_with_email(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@jordanmart.jo',
            'phone' => '0791234567',
            'password' => Hash::make('Secret123!'),
            'role' => 'customer',
        ]);

        $response = $this->post(route('login'), [
            'login' => 'customer@jordanmart.jo',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('storefront.home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_can_login_with_jordanian_phone(): void
    {
        $user = User::factory()->create([
            'email' => 'customer_phone@jordanmart.jo',
            'phone' => '0789876543',
            'password' => Hash::make('Secret123!'),
            'role' => 'customer',
        ]);

        // Attempt login using Jordanian phone format
        $response = $this->post(route('login'), [
            'login' => '0789876543',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('storefront.home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_customer_can_login_with_country_code_phone(): void
    {
        $user = User::factory()->create([
            'email' => 'phone_intl@jordanmart.jo',
            'phone' => '0775551234',
            'password' => Hash::make('Secret123!'),
            'role' => 'customer',
        ]);

        // Attempt login using +962 format
        $response = $this->post(route('login'), [
            'login' => '+962 77 555 1234',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect(route('storefront.home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_fail(): void
    {
        User::factory()->create([
            'email' => 'customer@jordanmart.jo',
            'password' => Hash::make('Secret123!'),
        ]);

        $response = $this->post(route('login'), [
            'login' => 'customer@jordanmart.jo',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_guest_can_view_register_form(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee(__('messages.register_title'));
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'طارق الزعبي',
            'email' => 'tareq@jordanmart.jo',
            'phone' => '0799887766',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect(route('storefront.home'));
        $this->assertDatabaseHas('users', [
            'email' => 'tareq@jordanmart.jo',
            'phone' => '0799887766',
            'role' => 'customer',
        ]);
        $this->assertAuthenticated();
    }

    public function test_guest_cart_merges_on_login(): void
    {
        $seller = Seller::factory()->create();
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $seller->id,
            'product_id' => $product->id,
            'price' => 15.000,
            'stock_quantity' => 20,
        ]);

        // 1. Guest adds item to cart
        $cartService = app(CartService::class);
        $cartService->addItem($offer->id, 2);
        $guestCart = $cartService->getActiveCart();

        $this->assertEquals(2, $guestCart->items->first()->quantity);

        // 2. User exists with their own account
        $user = User::factory()->create([
            'email' => 'merge_user@jordanmart.jo',
            'password' => Hash::make('Secret123!'),
        ]);

        // 3. User logs in
        $this->post(route('login'), [
            'login' => 'merge_user@jordanmart.jo',
            'password' => 'Secret123!',
        ]);

        // 4. Cart should now be associated with user
        $userCart = Cart::where('user_id', $user->id)->where('status', 'active')->first();
        $this->assertNotNull($userCart);
        $this->assertEquals(2, $userCart->items->first()->quantity);
    }

    public function test_customer_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('storefront.home'));
        $this->assertGuest();
    }
}
