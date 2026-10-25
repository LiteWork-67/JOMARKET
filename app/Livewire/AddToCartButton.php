<?php

namespace App\Livewire;

use App\Models\SellerOffer;
use App\Services\CartService;
use Livewire\Component;

class AddToCartButton extends Component
{
    public int $offerId;

    public int $quantity = 1;

    public string $buttonClass = '';

    public bool $isAdded = false;

    public bool $isBuyNow = false;

    public function mount(int $offerId, int $quantity = 1, string $buttonClass = '', bool $isBuyNow = false): void
    {
        $this->offerId = $offerId;
        $this->quantity = $quantity;
        $this->buttonClass = $buttonClass;
        $this->isBuyNow = $isBuyNow;
    }

    public function addToCart(CartService $cartService)
    {
        $offer = SellerOffer::find($this->offerId);

        if (! $offer || $offer->available_stock <= 0) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.out_of_stock'),
            ]);

            return;
        }

        $cartService->addItem($this->offerId, $this->quantity);

        $this->dispatch('cart-updated');
        $this->isAdded = true;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('messages.add_to_cart').' ✓',
        ]);
    }

    public function render()
    {
        $offer = SellerOffer::find($this->offerId);

        return view('livewire.add-to-cart-button', [
            'offer' => $offer,
        ]);
    }
}
