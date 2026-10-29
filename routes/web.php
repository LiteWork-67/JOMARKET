<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\OrderConfirmationController;
use App\Http\Controllers\Seller;
use App\Http\Controllers\SellerApplicationController;
use App\Http\Controllers\StorefrontController;
use App\Livewire\CartPage;
use App\Livewire\Checkout;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - JordanMart Multi-Vendor Marketplace
|--------------------------------------------------------------------------
*/

// Locale Switcher
Route::get('/locale/{lang}', [StorefrontController::class, 'setLocale'])->name('locale.set');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Cart & Checkout
Route::get('/cart', CartPage::class)->name('cart.index');
Route::get('/checkout', Checkout::class)->name('checkout.index');
Route::get('/orders/{orderNumber}/confirmation', [OrderConfirmationController::class, 'show'])->name('orders.confirmation');

// Customer Account Portal
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [Account\AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [Account\AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [Account\AccountController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{orderNumber}/cancel', [Account\AccountController::class, 'cancelOrder'])->name('orders.cancel');

    // Addresses Book
    Route::get('/addresses', [Account\AccountController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [Account\AccountController::class, 'storeAddress'])->name('addresses.store');
    Route::put('/addresses/{address}', [Account\AccountController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/addresses/{address}', [Account\AccountController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/addresses/{address}/default', [Account\AccountController::class, 'setDefaultAddress'])->name('addresses.default');

    // Profile & Password
    Route::get('/profile', [Account\AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [Account\AccountController::class, 'updateProfile'])->name('profile.update');

    // Wishlist
    Route::get('/wishlist', [Account\AccountController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle/{productId}', [Account\AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Product Reviews
    Route::post('/reviews', [Account\AccountController::class, 'storeReview'])->name('reviews.store');

    // Returns & Exchanges
    Route::get('/returns', [Account\ReturnRequestController::class, 'index'])->name('returns.index');
    Route::get('/returns/create/{sellerOrder}', [Account\ReturnRequestController::class, 'create'])->name('returns.create');
    Route::post('/returns/{sellerOrder}', [Account\ReturnRequestController::class, 'store'])->name('returns.store');
    Route::get('/returns/{returnRequest}', [Account\ReturnRequestController::class, 'show'])->name('returns.show');

    // Support Tickets
    Route::get('/tickets', [Account\SupportTicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [Account\SupportTicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [Account\SupportTicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{supportTicket}', [Account\SupportTicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{supportTicket}/reply', [Account\SupportTicketController::class, 'reply'])->name('tickets.reply');
});

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
    Route::get('/disputes', [Admin\DisputeAndRefundController::class, 'index'])->name('disputes.index');
    Route::get('/disputes/{returnRequest}', [Admin\DisputeAndRefundController::class, 'show'])->name('disputes.show');
    Route::post('/disputes/{returnRequest}/process', [Admin\DisputeAndRefundController::class, 'process'])->name('disputes.process');

    // Support Tickets & Customer Care
    Route::get('/support', [Admin\SupportTicketController::class, 'index'])->name('support.index');
    Route::get('/support/{supportTicket}', [Admin\SupportTicketController::class, 'show'])->name('support.show');
    Route::post('/support/{supportTicket}/reply', [Admin\SupportTicketController::class, 'reply'])->name('support.reply');
    Route::patch('/support/{supportTicket}/status', [Admin\SupportTicketController::class, 'updateStatus'])->name('support.status');

    // Financial Controls, Commission Reports & Ledger
    Route::get('/finance/commissions', [Admin\FinancialController::class, 'commissions'])->name('finance.commissions');
    Route::get('/finance/payouts', [Admin\FinancialController::class, 'payouts'])->name('finance.payouts');
    Route::post('/finance/payouts/{id}/approve', [Admin\FinancialController::class, 'approvePayout'])->name('finance.payouts.approve');
    Route::post('/finance/payouts/{id}/reject', [Admin\FinancialController::class, 'rejectPayout'])->name('finance.payouts.reject');
    Route::get('/finance/ledger', [Admin\FinancialController::class, 'ledger'])->name('finance.ledger');
    Route::get('/finance/cod', [Admin\CodReconciliationController::class, 'index'])->name('finance.cod.index');
    Route::patch('/finance/cod/{reconciliation}', [Admin\CodReconciliationController::class, 'updateStatus'])->name('finance.cod.update');

    // Categories & Brands
    Route::resource('categories', Admin\CategoryController::class);
    Route::resource('brands', Admin\BrandController::class);

    // Audit Trail
    Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
});
