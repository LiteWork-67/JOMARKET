<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Admin / Platform
            ['name' => 'Manage Sellers', 'slug' => 'sellers.manage', 'module' => 'sellers', 'description' => 'Approve, reject, or suspend sellers'],
            ['name' => 'Verify Documents', 'slug' => 'sellers.verify', 'module' => 'sellers', 'description' => 'Inspect and verify legal documents'],
            ['name' => 'Approve Payouts', 'slug' => 'sellers.payouts', 'module' => 'finance', 'description' => 'Approve seller settlement payouts'],
            ['name' => 'Moderate Products', 'slug' => 'catalog.moderate', 'module' => 'catalog', 'description' => 'Approve or reject catalog items'],
            ['name' => 'Manage Categories & Brands', 'slug' => 'catalog.manage', 'module' => 'catalog', 'description' => 'CRUD categories, brands, attributes'],
            ['name' => 'Manage Orders', 'slug' => 'orders.manage', 'module' => 'orders', 'description' => 'View all platform orders and dispute interventions'],
            ['name' => 'Process Refunds', 'slug' => 'orders.refund', 'module' => 'finance', 'description' => 'Authorize customer refunds'],
            ['name' => 'Financial Ledger Access', 'slug' => 'finance.ledger', 'module' => 'finance', 'description' => 'Inspect auditable financial ledger'],
            ['name' => 'Reconcile COD', 'slug' => 'finance.cod_reconcile', 'module' => 'finance', 'description' => 'Reconcile courier cash collections'],
            ['name' => 'Moderate Reviews', 'slug' => 'reviews.moderate', 'module' => 'reviews', 'description' => 'Approve or remove customer reviews'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'module' => 'admin', 'description' => 'Platform settings and CMS controls'],

            // Support
            ['name' => 'Support Tickets', 'slug' => 'support.tickets', 'module' => 'support', 'description' => 'Manage support tickets and chats'],
            ['name' => 'View Orders', 'slug' => 'orders.view', 'module' => 'orders', 'description' => 'View orders for customer support'],

            // Seller
            ['name' => 'Manage Store Offers', 'slug' => 'seller.offers.manage', 'module' => 'seller', 'description' => 'Create and edit seller offers and pricing'],
            ['name' => 'Manage Inventory', 'slug' => 'seller.inventory.manage', 'module' => 'seller', 'description' => 'Update stock and restock items'],
            ['name' => 'Manage Seller Orders', 'slug' => 'seller.orders.manage', 'module' => 'seller', 'description' => 'Process, pack, and ship orders'],
            ['name' => 'Request Payout', 'slug' => 'seller.payouts.request', 'module' => 'seller', 'description' => 'Request settlement payout to bank account'],
            ['name' => 'Manage Storefront', 'slug' => 'seller.store.manage', 'module' => 'seller', 'description' => 'Edit store profile, logo, banner'],
        ];

        $permissionModels = [];
        foreach ($permissions as $perm) {
            $permissionModels[$perm['slug']] = Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        // Roles
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Marketplace Administrator',
            'description' => 'Full control over JordanMart marketplace platform',
            'is_system' => true,
        ]);

        $supportRole = Role::firstOrCreate(['slug' => 'support'], [
            'name' => 'Customer Support Agent',
            'description' => 'Handle customer inquiries, tickets, and order tracking',
            'is_system' => true,
        ]);

        $sellerRole = Role::firstOrCreate(['slug' => 'seller'], [
            'name' => 'Merchant / Seller',
            'description' => 'Multi-vendor store owner and catalog manager',
            'is_system' => true,
        ]);

        $customerRole = Role::firstOrCreate(['slug' => 'customer'], [
            'name' => 'Customer',
            'description' => 'Marketplace shopper',
            'is_system' => true,
        ]);

        // Attach permissions to roles
        $adminRole->permissions()->sync(array_values(array_map(fn ($m) => $m->id, $permissionModels)));

        $supportRole->permissions()->sync([
            $permissionModels['support.tickets']->id,
            $permissionModels['orders.view']->id,
            $permissionModels['reviews.moderate']->id,
        ]);

        $sellerRole->permissions()->sync([
            $permissionModels['seller.offers.manage']->id,
            $permissionModels['seller.inventory.manage']->id,
            $permissionModels['seller.orders.manage']->id,
            $permissionModels['seller.payouts.request']->id,
            $permissionModels['seller.store.manage']->id,
        ]);
    }
}
