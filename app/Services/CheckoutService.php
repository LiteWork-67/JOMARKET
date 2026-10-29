<?php

namespace App\Services;

use App\Models\CouponRedemption;
use App\Models\InventoryMovement;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ParentOrder;
use App\Models\PaymentTransaction;
use App\Models\SellerOrder;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    /**
     * Process checkout atomically inside a database transaction.
     *
     * @param  array{
     *     contact_name: string,
     *     phone: string,
     *     email?: string,
     *     governorate: string,
     *     city: string,
     *     district?: string,
     *     street_address: string,
     *     building_number?: string,
     *     floor?: string,
     *     apartment?: string,
     *     landmark?: string,
     *     delivery_instructions?: string,
     *     customer_notes?: string,
     *     payment_method: string,
     *     payment_reference?: string,
     *     cliq_alias?: string
     * }  $checkoutData
     *
     * @throws Exception
     */
    public function processCheckout(User $user, array $checkoutData): ParentOrder
    {
        $cart = $this->cartService->getActiveCart();
        $cart->load(['items.offer.product', 'items.offer.seller']);

        if ($cart->items->isEmpty()) {
            throw new Exception(__('messages.empty_cart'));
        }

        return DB::transaction(function () use ($user, $cart, $checkoutData) {
            // 1. Verify stock availability for all items with lock
            foreach ($cart->items as $cartItem) {
                $offer = $cartItem->offer()->lockForUpdate()->first();
                if (! $offer || $offer->available_stock < $cartItem->quantity) {
                    $title = app()->getLocale() === 'ar' ? $offer->product->title_ar : $offer->product->title_en;
                    throw new Exception(__('messages.out_of_stock').': '.$title);
                }
            }

            // 2. Calculate totals and coupon
            $governorate = $checkoutData['governorate'] ?? null;
            $totals = $this->cartService->getTotals($governorate);
            $appliedCoupon = $totals['coupon'];

            // 3. Prepare Address Snapshot
            $shippingSnapshot = [
                'contact_name' => $checkoutData['contact_name'],
                'phone' => $checkoutData['phone'],
                'governorate' => $checkoutData['governorate'],
                'city' => $checkoutData['city'],
                'district' => $checkoutData['district'] ?? null,
                'street_address' => $checkoutData['street_address'],
                'building_number' => $checkoutData['building_number'] ?? null,
                'floor' => $checkoutData['floor'] ?? null,
                'apartment' => $checkoutData['apartment'] ?? null,
                'landmark' => $checkoutData['landmark'] ?? null,
                'delivery_instructions' => $checkoutData['delivery_instructions'] ?? null,
            ];

            // 4. Generate Unique Order Number: JM-YYYYMMDD-XXXXX
            $orderNumber = 'JM-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
            while (ParentOrder::where('order_number', $orderNumber)->exists()) {
                $orderNumber = 'JM-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
            }

            $paymentMethod = $checkoutData['payment_method'] === 'cliq' ? 'bank_transfer' : 'cash_on_delivery';
            $paymentRef = $checkoutData['payment_reference'] ?? ($checkoutData['payment_method'] === 'cliq' ? 'CLIQ-'.Str::upper(Str::random(8)) : null);

            // 5. Create Parent Order
            $parentOrder = ParentOrder::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal_amount' => $totals['subtotal'],
                'shipping_amount' => $totals['shipping'],
                'discount_amount' => $totals['discount'],
                'tax_amount' => 0.000,
                'total_amount' => $totals['total'],
                'currency' => 'JOD',
                'payment_status' => 'pending',
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentRef,
                'shipping_address_snapshot' => $shippingSnapshot,
                'billing_address_snapshot' => $shippingSnapshot,
                'customer_notes' => $checkoutData['customer_notes'] ?? null,
            ]);

            // 6. Split into Seller Orders by Seller Fulfillment Group
            $sellerGroups = $cart->items->groupBy(fn ($item) => $item->offer->seller_id);
            $totalSellers = $sellerGroups->count();

            foreach ($sellerGroups as $sellerId => $items) {
                $seller = $items->first()->offer->seller;
                $sellerSubtotal = round($items->sum(fn ($i) => (float) $i->unit_price * $i->quantity), 3);
                $commissionRate = (float) ($seller->commission_rate ?? 10.00);
                $commissionAmount = round($sellerSubtotal * ($commissionRate / 100), 3);
                $sellerNetAmount = round($sellerSubtotal - $commissionAmount, 3);

                // Share of shipping and discount
                $sellerShipping = $totalSellers > 0 ? round($totals['shipping'] / $totalSellers, 3) : 0.000;
                $sellerDiscount = $totals['subtotal'] > 0 ? round(($sellerSubtotal / $totals['subtotal']) * $totals['discount'], 3) : 0.000;
                $sellerTotal = max(0, round($sellerSubtotal + $sellerShipping - $sellerDiscount, 3));

                $sellerOrderNumber = $parentOrder->order_number.'-S'.$seller->id;

                $sellerOrder = SellerOrder::create([
                    'parent_order_id' => $parentOrder->id,
                    'seller_id' => $seller->id,
                    'order_number' => $sellerOrderNumber,
                    'status' => 'pending_confirmation',
                    'subtotal' => $sellerSubtotal,
                    'shipping_fee' => $sellerShipping,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'seller_net_amount' => $sellerNetAmount,
                    'discount_amount' => $sellerDiscount,
                    'tax_amount' => 0.000,
                    'total_amount' => $sellerTotal,
                    'shipping_method' => 'standard',
                ]);

                // 7. Create Order Items and Deduct Inventory
                foreach ($items as $cartItem) {
                    $offer = $cartItem->offer;
                    $itemSubtotal = round((float) $cartItem->unit_price * $cartItem->quantity, 3);
                    $itemCommission = round($itemSubtotal * ($commissionRate / 100), 3);
                    $itemNet = round($itemSubtotal - $itemCommission, 3);

                    // Deduct stock on SellerOffer
                    $offer->decrement('stock_quantity', $cartItem->quantity);
                    $balanceAfter = $offer->fresh()->available_stock;

                    // Record Inventory Movement
                    InventoryMovement::create([
                        'seller_offer_id' => $offer->id,
                        'type' => 'sale',
                        'quantity' => -$cartItem->quantity,
                        'balance_after' => $balanceAfter,
                        'reference_type' => SellerOrder::class,
                        'reference_id' => $sellerOrder->id,
                        'notes' => 'Sale for Order '.$sellerOrder->order_number,
                        'created_by' => $user->id,
                    ]);

                    // Create Order Item
                    OrderItem::create([
                        'seller_order_id' => $sellerOrder->id,
                        'seller_offer_id' => $offer->id,
                        'product_id' => $offer->product_id,
                        'product_variant_id' => $offer->product_variant_id,
                        'product_title_ar' => $offer->product->title_ar,
                        'product_title_en' => $offer->product->title_en,
                        'variant_title_ar' => $offer->variant?->title_ar,
                        'variant_title_en' => $offer->variant?->title_en,
                        'sku' => $offer->sku,
                        'unit_price' => $cartItem->unit_price,
                        'quantity' => $cartItem->quantity,
                        'subtotal' => $itemSubtotal,
                        'discount_amount' => 0.000,
                        'tax_amount' => 0.000,
                        'total_amount' => $itemSubtotal,
                        'commission_rate' => $commissionRate,
                        'commission_amount' => $itemCommission,
                        'seller_net_amount' => $itemNet,
                    ]);
                }

                // Initial Order Status History
                OrderStatusHistory::create([
                    'seller_order_id' => $sellerOrder->id,
                    'status_from' => null,
                    'status_to' => 'pending_confirmation',
                    'changed_by' => $user->id,
                    'notes' => 'Order created by customer',
                ]);
            }

            // 8. Payment Transaction Record
            PaymentTransaction::create([
                'parent_order_id' => $parentOrder->id,
                'amount' => $parentOrder->total_amount,
                'currency' => 'JOD',
                'provider' => $checkoutData['payment_method'] === 'cliq' ? 'cliq' : 'cod',
                'transaction_id' => $paymentRef,
                'status' => $checkoutData['payment_method'] === 'cliq' ? 'authorized' : 'initiated',
                'payload' => [
                    'method' => $checkoutData['payment_method'],
                    'cliq_alias' => $checkoutData['cliq_alias'] ?? null,
                ],
            ]);

            // 9. Coupon Redemption (if coupon applied)
            if ($appliedCoupon) {
                CouponRedemption::create([
                    'coupon_id' => $appliedCoupon->id,
                    'user_id' => $user->id,
                    'parent_order_id' => $parentOrder->id,
                    'discount_amount' => $totals['discount'],
                ]);

                $appliedCoupon->increment('usage_count');
            }

            // 10. Mark Cart converted and clean up session
            $cart->update(['status' => 'converted']);
            $this->cartService->removeCoupon();

            return $parentOrder;
        });
    }
}
