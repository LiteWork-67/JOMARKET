<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\SellerOffer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CartService
{
    public function getActiveCart(): Cart
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'active'],
                ['currency' => 'JOD']
            );

            // Merge guest cart if token exists in session
            $guestToken = Session::get('guest_cart_token');
            if ($guestToken) {
                $guestCart = Cart::where('guest_token', $guestToken)->where('status', 'active')->first();
                if ($guestCart && $guestCart->id !== $cart->id) {
                    foreach ($guestCart->items as $item) {
                        $existing = $cart->items()->where('seller_offer_id', $item->seller_offer_id)->first();
                        if ($existing) {
                            $existing->update(['quantity' => $existing->quantity + $item->quantity]);
                        } else {
                            $cart->items()->create([
                                'seller_offer_id' => $item->seller_offer_id,
                                'quantity' => $item->quantity,
                                'unit_price' => $item->unit_price,
                            ]);
                        }
                    }
                    $guestCart->update(['status' => 'merged']);
                    Session::forget('guest_cart_token');
                }
            }

            return $cart;
        }

        $guestToken = Session::get('guest_cart_token');
        if (! $guestToken) {
            $guestToken = Str::uuid()->toString();
            Session::put('guest_cart_token', $guestToken);
        }

        return Cart::firstOrCreate(
            ['guest_token' => $guestToken, 'status' => 'active'],
            ['currency' => 'JOD']
        );
    }

    public function addItem(int $offerId, int $quantity = 1): CartItem
    {
        $offer = SellerOffer::findOrFail($offerId);
        $cart = $this->getActiveCart();

        $item = $cart->items()->where('seller_offer_id', $offer->id)->first();

        if ($item) {
            $newQuantity = min($offer->available_stock, $item->quantity + $quantity);
            $item->update([
                'quantity' => $newQuantity,
                'unit_price' => $offer->effective_price,
            ]);
        } else {
            $newQuantity = min($offer->available_stock, max(1, $quantity));
            $item = $cart->items()->create([
                'seller_offer_id' => $offer->id,
                'quantity' => $newQuantity,
                'unit_price' => $offer->effective_price,
            ]);
        }

        return $item;
    }

    public function updateQuantity(int $itemId, int $quantity): ?CartItem
    {
        $cart = $this->getActiveCart();
        $item = $cart->items()->find($itemId);

        if (! $item) {
            return null;
        }

        if ($quantity <= 0) {
            $item->delete();

            return null;
        }

        $offer = $item->offer;
        $allowedQuantity = min($offer->available_stock, $quantity);

        $item->update([
            'quantity' => $allowedQuantity,
            'unit_price' => $offer->effective_price,
        ]);

        return $item;
    }

    public function removeItem(int $itemId): bool
    {
        $cart = $this->getActiveCart();
        $item = $cart->items()->find($itemId);

        if ($item) {
            $item->delete();

            return true;
        }

        return false;
    }

    public function applyCoupon(string $code): array
    {
        $cart = $this->getActiveCart();
        $cleanCode = strtoupper(trim($code));

        $coupon = Coupon::where('code', $cleanCode)->where('is_active', true)->first();

        if (! $coupon || ! $coupon->isValidForAmount($cart->subtotal)) {
            return [
                'success' => false,
                'message' => __('messages.invalid_coupon'),
            ];
        }

        Session::put('applied_coupon_code', $coupon->code);

        return [
            'success' => true,
            'coupon' => $coupon,
            'discount' => $coupon->calculateDiscount($cart->subtotal),
            'message' => __('messages.coupon_applied'),
        ];
    }

    public function removeCoupon(): void
    {
        Session::forget('applied_coupon_code');
    }

    public function getAppliedCoupon(): ?Coupon
    {
        $code = Session::get('applied_coupon_code');
        if (! $code) {
            return null;
        }

        $cart = $this->getActiveCart();
        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();

        if (! $coupon || ! $coupon->isValidForAmount($cart->subtotal)) {
            Session::forget('applied_coupon_code');

            return null;
        }

        return $coupon;
    }

    public function getTotals(?string $governorate = null): array
    {
        $cart = $this->getActiveCart();
        $subtotal = $cart->subtotal;

        if ($subtotal <= 0) {
            return [
                'subtotal' => 0.000,
                'shipping' => 0.000,
                'discount' => 0.000,
                'total' => 0.000,
                'coupon' => null,
            ];
        }

        // Free delivery on orders over 50 JOD across Jordan!
        if ($subtotal >= 50.000) {
            $shipping = 0.000;
        } else {
            $govLower = strtolower($governorate ?? '');
            if ($govLower === 'amman' || $governorate === 'عمّان') {
                $shipping = 2.000;
            } elseif (! empty($governorate)) {
                $shipping = 3.000;
            } else {
                $shipping = 2.000;
            }
        }

        $coupon = $this->getAppliedCoupon();
        $discount = $coupon ? $coupon->calculateDiscount($subtotal) : 0.000;

        $total = max(0, round($subtotal + $shipping - $discount, 3));

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'coupon' => $coupon,
        ];
    }

    public function clear(): void
    {
        $cart = $this->getActiveCart();
        $cart->items()->delete();
        $this->removeCoupon();
    }

    public function getItemsCount(): int
    {
        return $this->getActiveCart()->items->sum('quantity');
    }
}
