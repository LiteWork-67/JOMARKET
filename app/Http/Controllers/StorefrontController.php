<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerOffer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function setLocale(string $lang): RedirectResponse
    {
        if (in_array($lang, ['ar', 'en'], true)) {
            session(['locale' => $lang]);
        }

        return redirect()->back();
    }

    public function index(): View
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = Product::approved()
            ->where('is_featured', true)
            ->with(['bestOffer.seller', 'category', 'brand'])
            ->take(8)
            ->get();

        $deals = SellerOffer::where('is_active', true)
            ->whereNotNull('sale_price')
            ->where('stock_quantity', '>', 0)
            ->whereColumn('sale_price', '<', 'price')
            ->with(['product.category', 'seller'])
            ->take(6)
            ->get();

        $latestProducts = Product::approved()
            ->with(['bestOffer.seller', 'category', 'brand'])
            ->latest()
            ->take(8)
            ->get();

        $sellers = Seller::where('status', 'approved')
            ->orderByDesc('rating')
            ->take(6)
            ->get();

        $brands = Brand::where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        return view('storefront.index', compact(
            'categories',
            'featuredProducts',
            'deals',
            'latestProducts',
            'sellers',
            'brands'
        ));
    }

    public function catalog(Request $request): View
    {
        return view('storefront.catalog', [
            'initialSearch' => $request->query('q', ''),
            'initialCategory' => $request->query('category', ''),
            'initialBrand' => $request->query('brand', ''),
        ]);
    }

    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('storefront.catalog', [
            'initialSearch' => '',
            'initialCategory' => $category->slug,
            'initialBrand' => '',
            'category' => $category,
        ]);
    }

    public function brand(string $slug): View
    {
        $brand = Brand::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return view('storefront.catalog', [
            'initialSearch' => '',
            'initialCategory' => '',
            'initialBrand' => $brand->slug,
            'brand' => $brand,
        ]);
    }

    public function product(string $slug): View
    {
        $product = Product::approved()
            ->where('slug', $slug)
            ->with([
                'category',
                'brand',
                'images',
                'variants.attributeValues.attribute',
                'offers' => fn ($q) => $q->where('is_active', true)->with('seller'),
                'approvedReviews.user',
            ])
            ->firstOrFail();

        $product->increment('views_count');

        $relatedProducts = Product::approved()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['bestOffer.seller'])
            ->take(4)
            ->get();

        return view('storefront.product', compact('product', 'relatedProducts'));
    }

    public function sellerStore(string $slug): View
    {
        $seller = Seller::where('slug', $slug)
            ->where('status', 'approved')
            ->firstOrFail();

        $offers = SellerOffer::where('seller_id', $seller->id)
            ->where('is_active', true)
            ->with(['product.category', 'product.brand'])
            ->paginate(12);

        return view('storefront.seller-store', compact('seller', 'offers'));
    }

    public function searchSuggestions(Request $request)
    {
        $query = trim($request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $locale = app()->getLocale();
        $nameCol = $locale === 'ar' ? 'name_ar' : 'name_en';

        $products = Product::approved()
            ->where(function ($q) use ($query) {
                $q->where('name_ar', 'like', "%{$query}%")
                    ->orWhere('name_en', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->with('category')
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'type' => 'product',
                'title' => $p->name,
                'slug' => $p->slug,
                'url' => route('storefront.product', $p->slug),
                'category' => $p->category?->name,
            ]);

        $categories = Category::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name_ar', 'like', "%{$query}%")
                    ->orWhere('name_en', 'like', "%{$query}%");
            })
            ->take(3)
            ->get()
            ->map(fn ($c) => [
                'type' => 'category',
                'title' => $c->name,
                'slug' => $c->slug,
                'url' => route('storefront.category', $c->slug),
            ]);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
