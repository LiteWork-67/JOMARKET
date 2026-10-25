<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FinancialLedgerEntry;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\Role;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\SellerOrder;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $sellerUser;

    protected Seller $seller;

    protected User $otherSellerUser;

    protected Seller $otherSeller;

    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $sellerRole = Role::where('slug', 'seller')->first();
        $customerRole = Role::where('slug', 'customer')->first();

        // Approved Seller 1
        $this->sellerUser = User::factory()->create(['role' => 'seller']);
        $this->sellerUser->roles()->attach($sellerRole);
        $this->seller = Seller::factory()->create([
            'user_id' => $this->sellerUser->id,
            'status' => 'approved',
            'commission_rate' => 10.00,
        ]);

        // Approved Seller 2
        $this->otherSellerUser = User::factory()->create(['role' => 'seller']);
        $this->otherSellerUser->roles()->attach($sellerRole);
        $this->otherSeller = Seller::factory()->create([
            'user_id' => $this->otherSellerUser->id,
            'status' => 'approved',
            'commission_rate' => 10.00,
        ]);

        // Customer
        $this->customerUser = User::factory()->create(['role' => 'customer']);
        $this->customerUser->roles()->attach($customerRole);
    }

    public function test_guest_and_customer_cannot_access_seller_portal(): void
    {
        $this->get(route('seller.dashboard'))->assertRedirect();

        $this->actingAs($this->customerUser)
            ->get(route('seller.dashboard'))
            ->assertStatus(403);
    }

    public function test_unapproved_seller_cannot_access_seller_portal(): void
    {
        $pendingUser = User::factory()->create(['role' => 'seller']);
        Seller::factory()->create([
            'user_id' => $pendingUser->id,
            'status' => 'pending',
        ]);

        $this->actingAs($pendingUser)
            ->get(route('seller.dashboard'))
            ->assertStatus(403);
    }

    public function test_approved_seller_can_access_dashboard_with_real_metrics(): void
    {
        $response = $this->actingAs($this->sellerUser)
            ->get(route('seller.dashboard'))
            ->assertStatus(200)
            ->assertSee($this->seller->store_name);
    }

    public function test_seller_can_create_offer_for_approved_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'status' => 'approved']);

        $offerData = [
            'product_id' => $product->id,
            'seller_sku' => 'ATH-SKU-TEST-01',
            'condition' => 'new',
            'price' => 25.500,
            'sale_price' => 22.000,
            'stock_quantity' => 15,
            'handling_days' => 1,
            'min_order_quantity' => 1,
            'max_order_quantity' => 5,
            'is_active' => '1',
        ];

        $this->actingAs($this->sellerUser)
            ->post(route('seller.offers.store'), $offerData)
            ->assertRedirect(route('seller.offers.index'));

        $this->assertDatabaseHas('seller_offers', [
            'seller_id' => $this->seller->id,
            'seller_sku' => 'ATH-SKU-TEST-01',
            'price' => 25.500,
            'sale_price' => 22.000,
            'stock_quantity' => 15,
        ]);
    }

    public function test_seller_cannot_view_edit_or_delete_other_sellers_offer(): void
    {
        $product = Product::factory()->create();
        $otherOffer = SellerOffer::factory()->create([
            'seller_id' => $this->otherSeller->id,
            'product_id' => $product->id,
            'seller_sku' => 'OTHER-SKU-99',
            'price' => 99.000,
        ]);

        // Attempt edit
        $this->actingAs($this->sellerUser)
            ->get(route('seller.offers.edit', $otherOffer->id))
            ->assertStatus(404);

        // Attempt update
        $this->actingAs($this->sellerUser)
            ->put(route('seller.offers.update', $otherOffer->id), [
                'seller_sku' => 'HACKED-SKU',
                'condition' => 'new',
                'price' => 1.000,
                'stock_quantity' => 50,
            ])
            ->assertStatus(404);

        // Attempt delete
        $this->actingAs($this->sellerUser)
            ->delete(route('seller.offers.destroy', $otherOffer->id))
            ->assertStatus(404);

        // Verify other seller offer unchanged
        $this->assertDatabaseHas('seller_offers', [
            'id' => $otherOffer->id,
            'seller_sku' => 'OTHER-SKU-99',
        ]);
    }

    public function test_seller_can_manage_orders_and_status_transition_triggers_financial_ledger_credit(): void
    {
        $parentOrder = ParentOrder::factory()->create(['user_id' => $this->customerUser->id]);
        $sellerOrder = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $this->seller->id,
            'status' => 'processing',
            'subtotal' => 100.000,
            'commission_rate' => 10.00,
            'commission_amount' => 10.000,
            'seller_net_amount' => 90.000,
            'total_amount' => 100.000,
        ]);

        // Step 1: Mark as shipped
        $this->actingAs($this->sellerUser)
            ->patch(route('seller.orders.update-status', $sellerOrder->id), [
                'status' => 'shipped',
                'courier_name' => 'Aramex Jordan',
                'tracking_number' => 'AMX-9823412',
            ])
            ->assertRedirect();

        $sellerOrder->refresh();
        $this->assertEquals('shipped', $sellerOrder->status);
        $this->assertEquals('AMX-9823412', $sellerOrder->tracking_number);

        // Step 2: Mark as delivered -> MUST trigger financial ledger credit
        $this->actingAs($this->sellerUser)
            ->patch(route('seller.orders.update-status', $sellerOrder->id), [
                'status' => 'delivered',
            ])
            ->assertRedirect();

        $sellerOrder->refresh();
        $this->assertEquals('delivered', $sellerOrder->status);

        // Verify ledger entry
        $this->assertDatabaseHas('financial_ledger_entries', [
            'seller_id' => $this->seller->id,
            'type' => 'sale',
            'entry_type' => 'credit',
            'amount' => 90.000,
            'reference_type' => 'seller_order',
            'reference_id' => $sellerOrder->id,
        ]);

        $this->assertEquals(90.000, $this->seller->getSettledBalance());
    }

    public function test_seller_cannot_view_or_mutate_other_sellers_order(): void
    {
        $parentOrder = ParentOrder::factory()->create();
        $otherOrder = SellerOrder::factory()->create([
            'parent_order_id' => $parentOrder->id,
            'seller_id' => $this->otherSeller->id,
            'status' => 'processing',
        ]);

        // Attempt view
        $this->actingAs($this->sellerUser)
            ->get(route('seller.orders.show', $otherOrder->id))
            ->assertStatus(404);

        // Attempt update status
        $this->actingAs($this->sellerUser)
            ->patch(route('seller.orders.update-status', $otherOrder->id), [
                'status' => 'delivered',
            ])
            ->assertStatus(404);
    }

    public function test_seller_can_update_inventory_and_logs_movement(): void
    {
        $product = Product::factory()->create();
        $offer = SellerOffer::factory()->create([
            'seller_id' => $this->seller->id,
            'product_id' => $product->id,
            'stock_quantity' => 10,
        ]);

        $this->actingAs($this->sellerUser)
            ->patch(route('seller.inventory.update', $offer->id), [
                'adjustment' => 15,
                'reason' => 'Received supplier shipment batch #104',
            ])
            ->assertRedirect();

        $offer->refresh();
        $this->assertEquals(25, $offer->stock_quantity);

        $this->assertDatabaseHas('inventory_movements', [
            'seller_offer_id' => $offer->id,
            'type' => 'restock',
            'quantity' => 15,
            'balance_after' => 25,
        ]);
    }

    public function test_seller_payout_request_validation_against_settled_balance(): void
    {
        // Add credit to settled balance
        FinancialLedgerEntry::create([
            'seller_id' => $this->seller->id,
            'type' => 'sale',
            'entry_type' => 'credit',
            'amount' => 150.000,
            'currency' => 'JOD',
            'balance_after' => 150.000,
            'description' => 'Delivered order balance',
            'created_by' => $this->sellerUser->id,
        ]);

        $this->assertEquals(150.000, $this->seller->getSettledBalance());

        // Attempt to request more than settled balance
        $this->actingAs($this->sellerUser)
            ->post(route('seller.payouts.store'), [
                'amount' => 200.000,
                'notes' => 'Excess request',
            ])
            ->assertSessionHasErrors('amount');

        // Valid request
        $this->actingAs($this->sellerUser)
            ->post(route('seller.payouts.store'), [
                'amount' => 100.000,
                'notes' => 'Transfer to Arab Bank',
            ])
            ->assertRedirect(route('seller.payouts.index'));

        $this->assertDatabaseHas('seller_payout_requests', [
            'seller_id' => $this->seller->id,
            'amount' => 100.000,
            'status' => 'requested',
        ]);
    }

    public function test_customer_can_submit_seller_application(): void
    {
        $applicationData = [
            'store_name_ar' => 'متجر البتراء الحديث',
            'store_name_en' => 'Petra Modern Hub',
            'commercial_registration_no' => 'CR-778899',
            'tax_number' => 'TAX-112233',
            'contact_email' => 'contact@petramodern.jo',
            'contact_phone' => '+962791234567',
            'governorate' => 'Amman',
            'city' => 'Sweifieh',
            'description_ar' => 'متجر لبيع الإلكترونيات في عمان',
        ];

        $this->actingAs($this->customerUser)
            ->post(route('seller.apply.store'), $applicationData)
            ->assertRedirect(route('seller.apply'));

        $this->assertDatabaseHas('sellers', [
            'user_id' => $this->customerUser->id,
            'store_name_en' => 'Petra Modern Hub',
            'status' => 'pending',
        ]);
    }
}
