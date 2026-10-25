<?php

namespace Tests\Feature;

use App\Livewire\AddToCartButton;
use App\Livewire\CartCounter;
use App\Livewire\ProductCatalog;
use App\Livewire\ProductVariantSelector;
use App\Livewire\SearchBar;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_homepage_renders_arabic_rtl_storefront_with_products(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('JordanMart');
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('تسوق حسب الفئات');
        $response->assertSee('عروض وتخفيضات اليوم');
        $response->assertSee('د.أ');
    }

    public function test_locale_switcher_toggles_between_arabic_rtl_and_english_ltr(): void
    {
        // Switch to English
        $responseEn = $this->get('/locale/en');
        $responseEn->assertRedirect();

        $pageEn = $this->withSession(['locale' => 'en'])->get('/');
        $pageEn->assertStatus(200);
        $pageEn->assertSee('dir="ltr"', false);
        $pageEn->assertSee('All Categories');
        $pageEn->assertSee('JOD');

        // Switch to Arabic
        $responseAr = $this->get('/locale/ar');
        $responseAr->assertRedirect();

        $pageAr = $this->withSession(['locale' => 'ar'])->get('/');
        $pageAr->assertStatus(200);
        $pageAr->assertSee('dir="rtl"', false);
        $pageAr->assertSee('جميع الفئات');
    }

    public function test_catalog_page_renders_livewire_component(): void
    {
        $response = $this->get('/catalog');

        $response->assertStatus(200);
        $response->assertSeeLivewire(ProductCatalog::class);
    }

    public function test_livewire_product_catalog_filters_by_search_query(): void
    {
        $iphone = Product::where('slug', 'apple-iphone-16-pro-max-256gb')->firstOrFail();
        $nike = Product::where('slug', 'nike-air-max-pulse-shoes')->firstOrFail();

        Livewire::test(ProductCatalog::class)
            ->set('search', 'iPhone')
            ->assertSee($iphone->name)
            ->assertDontSee($nike->name);
    }

    public function test_livewire_product_catalog_filters_by_category(): void
    {
        $iphone = Product::where('slug', 'apple-iphone-16-pro-max-256gb')->firstOrFail();
        $samsung = Product::where('slug', 'samsung-galaxy-s25-ultra-512gb')->firstOrFail();
        $dior = Product::where('slug', 'dior-sauvage-eau-de-parfum-100ml')->firstOrFail();

        Livewire::test(ProductCatalog::class)
            ->set('category', 'electronics')
            ->assertSee($iphone->name)
            ->assertSee($samsung->name)
            ->assertDontSee($dior->name);
    }

    public function test_livewire_search_bar_provides_instant_suggestions(): void
    {
        $samsung = Product::where('slug', 'samsung-galaxy-s25-ultra-512gb')->firstOrFail();

        Livewire::test(SearchBar::class)
            ->set('query', 'Galaxy')
            ->assertSet('showDropdown', true)
            ->assertSee($samsung->name);
    }

    public function test_product_detail_page_renders_with_multi_vendor_selector(): void
    {
        $product = Product::where('slug', 'apple-iphone-16-pro-max-256gb')->firstOrFail();

        $response = $this->get('/product/'.$product->slug);

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSeeLivewire(ProductVariantSelector::class);
        $response->assertSee('كفالة أصلية');
        $response->assertSee('شحن سريع');
        $response->assertSee('الدفع عند الاستلام');
    }

    public function test_seller_public_storefront_renders_seller_profile_and_offers(): void
    {
        $seller = Seller::where('slug', 'amman-tech-hub')->firstOrFail();
        $iphone = Product::where('slug', 'apple-iphone-16-pro-max-256gb')->firstOrFail();

        $response = $this->get('/shops/'.$seller->slug);

        $response->assertStatus(200);
        $response->assertSee($seller->store_name);
        $response->assertSee('تاجر موثق رسمياً');
        $response->assertSee($iphone->name);
    }

    public function test_livewire_add_to_cart_button_adds_item_and_updates_cart_counter(): void
    {
        $offer = SellerOffer::where('stock_quantity', '>', 0)->firstOrFail();

        Livewire::test(CartCounter::class)
            ->assertSee('0');

        Livewire::test(AddToCartButton::class, ['offerId' => $offer->id])
            ->call('addToCart')
            ->assertDispatched('cart-updated')
            ->assertSet('isAdded', true);

        Livewire::test(CartCounter::class)
            ->dispatch('cart-updated')
            ->assertSee('1');
    }
}
