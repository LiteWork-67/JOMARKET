<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Seller;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - JordanMart Multi-Vendor Marketplace
|--------------------------------------------------------------------------
*/

// Locale Switcher
Route::get('/locale/{lang}', [StorefrontController::class, 'setLocale'])->name('locale.set');

// Auth Fallback for Guest Redirects
Route::get('/login', fn () => redirect()->route('storefront.home'))->name('login');

// Storefront & Catalog
Route::get('/', [StorefrontController::class, 'index'])->name('storefront.home');
Route::get('/catalog', [StorefrontController::class, 'catalog'])->name('storefront.catalog');
Route::get('/category/{slug}', [StorefrontController::class, 'category'])->name('storefront.category');
Route::get('/brand/{slug}', [StorefrontController::class, 'brand'])->name('storefront.brand');
Route::get('/product/{slug}', [StorefrontController::class, 'product'])->name('storefront.product');
Route::get('/shops/{slug}', [StorefrontController::class, 'sellerStore'])->name('storefront.seller');

// Search Suggestions API
Route::get('/api/search-suggestions', [StorefrontController::class, 'searchSuggestions'])->name('api.search_suggestions');

// Seller Onboarding & Application
Route::middleware('auth')->group(function () {
    Route::get('/seller/apply', [SellerApplicationController::class, 'create'])->name('seller.apply');
    Route::post('/seller/apply', [SellerApplicationController::class, 'store'])->name('seller.apply.store');
});

// Seller Portal (Enforces seller ownership)
Route::prefix('seller')->name('seller.')->middleware(['auth', 'seller'])->group(function () {
    Route::get('/dashboard', [Seller\DashboardController::class, 'index'])->name('dashboard');

    // Offers & Catalog
    Route::resource('offers', Seller\OfferController::class)->except(['show']);

    // Order Fulfillment
    Route::get('/orders', [Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [Seller\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/status', [Seller\OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Inventory Management
    Route::get('/inventory', [Seller\InventoryController::class, 'index'])->name('inventory.index');
    Route::patch('/inventory/{id}', [Seller\InventoryController::class, 'updateStock'])->name('inventory.update');

    // Financial Settlements & Payouts
    Route::get('/payouts', [Seller\PayoutController::class, 'index'])->name('payouts.index');
    Route::get('/payouts/create', [Seller\PayoutController::class, 'create'])->name('payouts.create');
    Route::post('/payouts', [Seller\PayoutController::class, 'store'])->name('payouts.store');

    // Store Profile
    Route::get('/profile', [Seller\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Seller\ProfileController::class, 'update'])->name('profile.update');
});

// Admin Dashboard
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Seller Management
    Route::get('/sellers', [Admin\SellerManagementController::class, 'index'])->name('sellers.index');
    Route::get('/sellers/{id}', [Admin\SellerManagementController::class, 'show'])->name('sellers.show');
    Route::post('/sellers/{id}/manage', [Admin\SellerManagementController::class, 'manage'])->name('sellers.manage');

    // Catalog & Product Moderation
    Route::get('/products', [Admin\ProductModerationController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [Admin\ProductModerationController::class, 'show'])->name('products.show');
    Route::post('/products/{id}/moderate', [Admin\ProductModerationController::class, 'moderate'])->name('products.moderate');

    // Orders & Disputes
    Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [Admin\OrderController::class, 'show'])->name('orders.show');

    // Financial Controls, Commission Reports & Ledger
    Route::get('/finance/commissions', [Admin\FinancialController::class, 'commissions'])->name('finance.commissions');
    Route::get('/finance/payouts', [Admin\FinancialController::class, 'payouts'])->name('finance.payouts');
    Route::post('/finance/payouts/{id}/approve', [Admin\FinancialController::class, 'approvePayout'])->name('finance.payouts.approve');
    Route::post('/finance/payouts/{id}/reject', [Admin\FinancialController::class, 'rejectPayout'])->name('finance.payouts.reject');
    Route::get('/finance/ledger', [Admin\FinancialController::class, 'ledger'])->name('finance.ledger');

    // Categories & Brands
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('brands', Admin\BrandController::class);

    // Audit Trail
    Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
});
