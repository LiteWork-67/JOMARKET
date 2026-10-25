<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $category = '';

    public string $brand = '';

    public ?float $minPrice = null;

    public ?float $maxPrice = null;

    public bool $inStockOnly = false;

    public string $condition = 'all';

    public string $sortBy = 'relevance';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'brand' => ['except' => ''],
        'sortBy' => ['except' => 'relevance'],
    ];

    public function mount(string $initialSearch = '', string $initialCategory = '', string $initialBrand = ''): void
    {
        $this->search = $initialSearch;
        $this->category = $initialCategory;
        $this->brand = $initialBrand;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingBrand(): void
    {
        $this->resetPage();
    }

    public function updatingSortBy(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category', 'brand', 'minPrice', 'maxPrice', 'inStockOnly', 'condition', 'sortBy']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::approved()
            ->with(['bestOffer.seller', 'category', 'brand']);

        if (! empty($this->search)) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name_ar', 'like', "%{$s}%")
                    ->orWhere('name_en', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%")
                    ->orWhere('sku_prefix', 'like', "%{$s}%");
            });
        }

        if (! empty($this->category)) {
            $cat = Category::where('slug', $this->category)->first();
            if ($cat) {
                $categoryIds = Category::where('parent_id', $cat->id)->pluck('id')->push($cat->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        if (! empty($this->brand)) {
            $brandModel = Brand::where('slug', $this->brand)->first();
            if ($brandModel) {
                $query->where('brand_id', $brandModel->id);
            }
        }

        if ($this->minPrice !== null && $this->minPrice > 0) {
            $query->whereHas('offers', fn ($q) => $q->where('price', '>=', $this->minPrice));
        }

        if ($this->maxPrice !== null && $this->maxPrice > 0) {
            $query->whereHas('offers', fn ($q) => $q->where('price', '<=', $this->maxPrice));
        }

        if ($this->inStockOnly) {
            $query->whereHas('offers', fn ($q) => $q->where('stock_quantity', '>', 0));
        }

        if ($this->condition !== 'all') {
            $query->whereHas('offers', fn ($q) => $q->where('condition', $this->condition));
        }

        switch ($this->sortBy) {
            case 'price_asc':
                $query->leftJoin('seller_offers', 'products.id', '=', 'seller_offers.product_id')
                    ->orderBy('seller_offers.price', 'asc')
                    ->select('products.*')
                    ->distinct();
                break;
            case 'price_desc':
                $query->leftJoin('seller_offers', 'products.id', '=', 'seller_offers.product_id')
                    ->orderBy('seller_offers.price', 'desc')
                    ->select('products.*')
                    ->distinct();
                break;
            case 'newest':
                $query->latest();
                break;
            case 'rating':
                $query->orderByDesc('views_count');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        $categories = Category::whereNull('parent_id')->where('is_active', true)->with('children')->get();
        $brands = Brand::where('is_active', true)->orderBy('name_en')->get();

        return view('livewire.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
