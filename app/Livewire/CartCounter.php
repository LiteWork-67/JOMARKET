<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCounter extends Component
{
    public int $count = 0;

    public function mount(CartService $cartService): void
    {
        $this->count = $cartService->getItemsCount();
    }

    #[On('cart-updated')]
    public function updateCount(CartService $cartService): void
    {
        $this->count = $cartService->getItemsCount();
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
