<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FinancialLedgerEntry;
use App\Models\Product;
use App\Models\Role;
use App\Models\Seller;
use App\Models\SellerPayoutRequest;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $sellerUser;

    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleAndPermissionSeeder::class);

        $adminRole = Role::where('slug', 'admin')->first();
        $sellerRole = Role::where('slug', 'seller')->first();
        $customerRole = Role::where('slug', 'customer')->first();

        // Admin
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->adminUser->roles()->attach($adminRole);

        // Seller
        $this->sellerUser = User::factory()->create(['role' => 'seller']);
        $this->sellerUser->roles()->attach($sellerRole);
        Seller::factory()->create(['user_id' => $this->sellerUser->id, 'status' => 'approved']);

        // Customer
        $this->customerUser = User::factory()->create(['role' => 'customer']);
        $this->customerUser->roles()->attach($customerRole);
    }

    public function test_customer_and_seller_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->customerUser)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);

        $this->actingAs($this->sellerUser)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_with_real_metrics(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSee('لوحة المؤشرات والتحليلات');
    }

    public function test_admin_can_approve_pending_seller_and_assign_commission(): void
    {
        $applicant = User::factory()->create(['role' => 'customer']);
        $seller = Seller::factory()->create([
            'user_id' => $applicant->id,
            'status' => 'pending',
            'commission_rate' => 10.00,
        ]);

        $this->actingAs($this->adminUser)
            ->post(route('admin.sellers.manage', $seller->id), [
                'action' => 'approve',
                'commission_rate' => 8.50,
            ])
            ->assertRedirect();

        $seller->refresh();
        $this->assertEquals('approved', $seller->status);
        $this->assertEquals(8.50, $seller->commission_rate);
        $this->assertNotNull($seller->verified_at);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.seller.approve',
            'auditable_type' => Seller::class,
            'auditable_id' => $seller->id,
        ]);
    }

    public function test_admin_can_moderate_pending_product(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'pending_approval',
            'is_active' => false,
        ]);

        // Approve product
        $this->actingAs($this->adminUser)
            ->post(route('admin.products.moderate', $product->id), [
                'action' => 'approve',
            ])
            ->assertRedirect();

        $product->refresh();
        $this->assertEquals('approved', $product->status);
        $this->assertTrue($product->is_active);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'admin.product.approve',
            'auditable_type' => Product::class,
            'auditable_id' => $product->id,
        ]);
    }

    public function test_admin_can_approve_payout_and_records_debit_in_financial_ledger(): void
    {
        $seller = $this->sellerUser->seller;

        // Credit 300 JOD to seller ledger
        FinancialLedgerEntry::create([
            'seller_id' => $seller->id,
            'type' => 'sale',
            'entry_type' => 'credit',
            'amount' => 300.000,
            'currency' => 'JOD',
            'balance_after' => 300.000,
            'description' => 'Delivered order earnings',
            'created_by' => $this->sellerUser->id,
        ]);

        $payout = SellerPayoutRequest::create([
            'seller_id' => $seller->id,
            'amount' => 200.000,
            'currency' => 'JOD',
            'status' => 'requested',
            'requested_by' => $this->sellerUser->id,
        ]);

        // Approve payout
        $this->actingAs($this->adminUser)
            ->post(route('admin.finance.payouts.approve', $payout->id))
            ->assertRedirect();

        $payout->refresh();
        $this->assertEquals('completed', $payout->status);
        $this->assertEquals($this->adminUser->id, $payout->approved_by);

        // Verify debit recorded in financial ledger
        $this->assertDatabaseHas('financial_ledger_entries', [
            'seller_id' => $seller->id,
            'type' => 'payout',
            'entry_type' => 'debit',
            'amount' => 200.000,
            'reference_type' => 'seller_payout_request',
            'reference_id' => $payout->id,
        ]);

        $this->assertEquals(100.000, $seller->getSettledBalance());
    }

    public function test_admin_can_create_and_manage_categories(): void
    {
        $categoryData = [
            'name_ar' => 'الأجهزة المنزلية الذكية',
            'name_en' => 'Smart Home Appliances',
            'slug' => 'smart-home-appliances',
            'commission_rate' => 7.5,
            'is_active' => '1',
        ];

        $this->actingAs($this->adminUser)
            ->post(route('admin.categories.store'), $categoryData)
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'slug' => 'smart-home-appliances',
            'commission_rate' => 7.5,
        ]);
    }
}
