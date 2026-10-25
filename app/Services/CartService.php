<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
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

    public function getItemsCount(): int
    {
        return $this->getActiveCart()->items->sum('quantity');
    }
}
