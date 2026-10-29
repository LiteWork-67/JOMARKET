<?php

namespace App\Livewire;

use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.storefront')]
class CartPage extends Component
{
    public string $couponCode = '';

    public string $couponMessage = '';

    public string $couponError = '';

    public function incrementQuantity(int $itemId, CartService $cartService): void
    {
        $cart = $cartService->getActiveCart();
        $item = $cart->items()->find($itemId);

        if ($item) {
            $cartService->updateQuantity($itemId, $item->quantity + 1);
            $this->dispatch('cart-updated');
        }
    }

    public function decrementQuantity(int $itemId, CartService $cartService): void
    {
        $cart = $cartService->getActiveCart();
        $item = $cart->items()->find($itemId);

        if ($item) {
            if ($item->quantity > 1) {
                $cartService->updateQuantity($itemId, $item->quantity - 1);
            } else {
                $cartService->removeItem($itemId);
            }
            $this->dispatch('cart-updated');
        }
    }

    public function removeItem(int $itemId, CartService $cartService): void
    {
        $cartService->removeItem($itemId);
        $this->dispatch('cart-updated');
        $this->dispatch('notify', [
            'type' => 'info',
            'message' => __('messages.item_removed'),
        ]);
    }

    public function applyCoupon(CartService $cartService): void
    {
        $this->couponError = '';
        $this->couponMessage = '';

        if (empty(trim($this->couponCode))) {
            $this->couponError = __('messages.invalid_coupon');

            return;
        }

        $result = $cartService->applyCoupon($this->couponCode);

        if ($result['success']) {
            $this->couponMessage = $result['message'];
            $this->couponCode = '';
        } else {
            $this->couponError = $result['message'];
        }
    }

    public function removeCoupon(CartService $cartService): void
    {
        $cartService->removeCoupon();
        $this->couponMessage = '';
        $this->couponError = '';
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getActiveCart();
        $cart->load(['items.offer.product.brand', 'items.offer.seller']);

        // Group items by seller for multi-vendor fulfillment grouping
        $sellerGroups = $cart->items->groupBy(fn ($item) => $item->offer->seller_id);

        $totals = $cartService->getTotals();

        return view('livewire.cart-page', [
            'cart' => $cart,
            'sellerGroups' => $sellerGroups,
            'totals' => $totals,
            'appliedCoupon' => $cartService->getAppliedCoupon(),
        ]);
    }
}
