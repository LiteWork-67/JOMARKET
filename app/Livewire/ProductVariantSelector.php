<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\SellerOffer;
use Livewire\Component;

class ProductVariantSelector extends Component
{
    public int $productId;

    public ?int $selectedOfferId = null;

    public int $quantity = 1;

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        $product = Product::with('activeOffers.seller')->find($productId);

        if ($product && $product->activeOffers->isNotEmpty()) {
            $this->selectedOfferId = $product->activeOffers->first()->id;
        }
    }

    public function selectOffer(int $offerId): void
    {
        $this->selectedOfferId = $offerId;
        $this->quantity = 1;
    }

    public function incrementQty(): void
    {
        $offer = SellerOffer::find($this->selectedOfferId);
        if ($offer && $this->quantity < min($offer->available_stock, $offer->max_order_quantity)) {
            $this->quantity++;
        }
    }

    public function decrementQty(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function render()
    {
        $product = Product::with([
            'category',
            'brand',
            'offers' => fn ($q) => $q->where('is_active', true)->with('seller'),
        ])->findOrFail($this->productId);

        $selectedOffer = SellerOffer::with('seller')->find($this->selectedOfferId);

        return view('livewire.product-variant-selector', [
            'product' => $product,
            'selectedOffer' => $selectedOffer,
        ]);
    }
}
