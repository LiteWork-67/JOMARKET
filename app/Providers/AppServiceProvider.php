<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\SellerOrder;
use App\Models\SellerPayoutRequest;
use App\Policies\ProductPolicy;
use App\Policies\SellerOfferPolicy;
use App\Policies\SellerOrderPolicy;
use App\Policies\SellerPayoutRequestPolicy;
use App\Policies\SellerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(SellerOffer::class, SellerOfferPolicy::class);
        Gate::policy(SellerOrder::class, SellerOrderPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Seller::class, SellerPolicy::class);
        Gate::policy(SellerPayoutRequest::class, SellerPayoutRequestPolicy::class);
    }
}
