<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class SearchBar extends Component
{
    public string $query = '';

    public string $selectedCategory = '';

    public array $suggestions = [];

    public bool $showDropdown = false;

    public function updatedQuery(): void
    {
        $q = trim($this->query);

        if (mb_strlen($q) < 2) {
            $this->suggestions = [];
            $this->showDropdown = false;

            return;
        }

        $products = Product::approved()
            ->where(function ($query) use ($q) {
                $query->where('name_ar', 'like', "%{$q}%")
                    ->orWhere('name_en', 'like', "%{$q}%")
                    ->orWhere('barcode', 'like', "%{$q}%");
            })
            ->with(['category', 'bestOffer'])
            ->take(5)
            ->get();

        $this->suggestions = $products->map(fn ($p) => [
            'title' => $p->name,
            'slug' => $p->slug,
            'category' => $p->category?->name,
            'price' => $p->bestOffer ? number_format($p->bestOffer->effective_price, 3) : null,
            'image' => $p->main_image_url,
            'url' => route('storefront.product', $p->slug),
        ])->toArray();

        $this->showDropdown = true;
    }

    public function submitSearch()
    {
        $this->showDropdown = false;

        return redirect()->route('storefront.catalog', [
            'q' => $this->query,
            'category' => $this->selectedCategory,
        ]);
    }

    public function hideDropdown(): void
    {
        $this->showDropdown = false;
    }

    public function render()
    {
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();

        return view('livewire.search-bar', [
            'categories' => $categories,
        ]);
    }
}
