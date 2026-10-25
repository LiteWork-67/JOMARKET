<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_admin_has_full_permissions(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::factory()->admin()->create();
        $admin->roles()->attach($adminRole);

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasPermission('sellers.manage'));
        $this->assertTrue($admin->hasPermission('sellers.payouts'));
        $this->assertTrue($admin->hasPermission('finance.ledger'));
    }

    public function test_support_user_has_restricted_permissions(): void
    {
        $supportRole = Role::where('slug', 'support')->first();
        $support = User::factory()->support()->create();
        $support->roles()->attach($supportRole);

        $this->assertTrue($support->isSupport());
        $this->assertFalse($support->isAdmin());
        $this->assertTrue($support->hasPermission('support.tickets'));
        $this->assertTrue($support->hasPermission('orders.view'));
        // Support agent must never have access to sensitive financial controls
        $this->assertFalse($support->hasPermission('sellers.payouts'));
        $this->assertFalse($support->hasPermission('finance.ledger'));
    }

    public function test_seller_has_seller_permissions_only(): void
    {
        $sellerRole = Role::where('slug', 'seller')->first();
        $seller = User::factory()->seller()->create();
        $seller->roles()->attach($sellerRole);

        $this->assertTrue($seller->isSeller());
        $this->assertFalse($seller->isAdmin());
        $this->assertTrue($seller->hasPermission('seller.offers.manage'));
        $this->assertTrue($seller->hasPermission('seller.orders.manage'));
        $this->assertFalse($seller->hasPermission('sellers.payouts'));
    }

    public function test_customer_has_no_administrative_permissions(): void
    {
        $customerRole = Role::where('slug', 'customer')->first();
        $customer = User::factory()->create();
        $customer->roles()->attach($customerRole);

        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->isAdmin());
        $this->assertFalse($customer->isSeller());
        $this->assertFalse($customer->hasPermission('catalog.moderate'));
        $this->assertFalse($customer->hasPermission('sellers.manage'));
    }
}
