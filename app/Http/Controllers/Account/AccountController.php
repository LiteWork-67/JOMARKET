<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Cancellation;
use App\Models\InventoryMovement;
use App\Models\OrderStatusHistory;
use App\Models\ParentOrder;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $recentOrders = $user->orders()
            ->with(['sellerOrders.items.product', 'sellerOrders.seller'])
            ->latest()
            ->take(5)
            ->get();

        $defaultAddress = $user->defaultShippingAddress()->first()
            ?? $user->addresses()->latest()->first();

        $stats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->whereIn('status', ['pending', 'processing'])->count(),
            'addresses_count' => $user->addresses()->count(),
            'wishlist_count' => Wishlist::where('user_id', $user->id)->count(),
        ];

        return view('account.dashboard', [
            'user' => $user,
            'recentOrders' => $recentOrders,
            'defaultAddress' => $defaultAddress,
            'stats' => $stats,
        ]);
    }

    public function orders(): View
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with(['sellerOrders.seller', 'sellerOrders.items.product'])
            ->latest()
            ->paginate(10);

        return view('account.orders', [
            'orders' => $orders,
        ]);
    }

    public function showOrder(string $orderNumber): View
    {
        $order = ParentOrder::where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->with([
                'sellerOrders.seller',
                'sellerOrders.items.product',
                'sellerOrders.statusHistories',
                'paymentTransactions',
            ])
            ->firstOrFail();

        return view('account.order-detail', [
            'order' => $order,
        ]);
    }

    public function cancelOrder(string $orderNumber, Request $request): RedirectResponse
    {
        $order = ParentOrder::where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        if ($order->status !== 'pending') {
            return back()->with('error', __('messages.order_cannot_be_cancelled'));
        }

        $reason = $request->input('reason', 'Customer requested cancellation');

        DB::transaction(function () use ($order, $reason) {
            $order->update(['status' => 'cancelled']);

            foreach ($order->sellerOrders as $sellerOrder) {
                if ($sellerOrder->status !== 'cancelled') {
                    $sellerOrder->update([
                        'status' => 'cancelled',
                        'cancellation_reason' => $reason,
                        'cancelled_by' => Auth::id(),
                    ]);

                    // Restore inventory
                    foreach ($sellerOrder->items as $item) {
                        $offer = $item->offer;
                        if ($offer) {
                            $offer->increment('stock_quantity', $item->quantity);
                            $balanceAfter = $offer->fresh()->available_stock;

                            InventoryMovement::create([
                                'seller_offer_id' => $offer->id,
                                'type' => 'release',
                                'quantity' => $item->quantity,
                                'balance_after' => $balanceAfter,
                                'reference_type' => ParentOrder::class,
                                'reference_id' => $order->id,
                                'notes' => 'Customer cancelled order '.$order->order_number,
                                'created_by' => Auth::id(),
                            ]);
                        }
                    }

                    OrderStatusHistory::create([
                        'seller_order_id' => $sellerOrder->id,
                        'status_from' => $sellerOrder->getOriginal('status'),
                        'status_to' => 'cancelled',
                        'changed_by' => Auth::id(),
                        'notes' => 'Cancelled by customer: '.$reason,
                    ]);

                    Cancellation::create([
                        'seller_order_id' => $sellerOrder->id,
                        'requested_by' => Auth::id(),
                        'reason' => $reason,
                        'status' => 'approved',
                        'processed_by' => Auth::id(),
                        'processed_at' => now(),
                    ]);
                }
            }
        });

        return back()->with('success', __('messages.order_cancelled_success'));
    }

    public function addresses(): View
    {
        $addresses = Auth::user()->addresses()->latest()->get();

        $governorates = [
            'عمّان' => 'عمّان (العاصمة)',
            'الزرقاء' => 'الزرقاء',
            'إربد' => 'إربد',
            'العقبة' => 'العقبة',
            'البلقاء' => 'البلقاء (السلط)',
            'مأدبا' => 'مأدبا',
            'المفرق' => 'المفرق',
            'جرش' => 'جرش',
            'عجلون' => 'عجلون',
            'الكرك' => 'الكرك',
            'الطفيلة' => 'الطفيلة',
            'معان' => 'معان',
        ];

        return view('account.addresses', [
            'addresses' => $addresses,
            'governorates' => $governorates,
        ]);
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'governorate' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'building_number' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();
        $isDefault = $request->boolean('is_default') || $user->addresses()->count() === 0;

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $user->addresses()->create([
            ...$validated,
            'district' => $request->input('district') ?: $request->input('city'),
            'type' => 'shipping',
            'is_default' => $isDefault,
        ]);

        return back()->with('success', __('messages.address_added'));
    }

    public function updateAddress(Address $address, Request $request): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'contact_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'governorate' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'building_number' => ['nullable', 'string', 'max:50'],
            'floor' => ['nullable', 'string', 'max:50'],
            'apartment' => ['nullable', 'string', 'max:50'],
            'landmark' => ['nullable', 'string', 'max:255'],
            'delivery_instructions' => ['nullable', 'string', 'max:500'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('is_default')) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $validated['district'] = $request->input('district') ?: ($validated['city'] ?? $address->city);

        $address->update($validated);

        return back()->with('success', __('messages.address_updated'));
    }

    public function deleteAddress(Address $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', __('messages.address_deleted'));
    }

    public function setDefaultAddress(Address $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        Auth::user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', __('messages.default_address_set'));
    }

    public function profile(): View
    {
        return view('account.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,'.$user->id],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];

        if (! empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', __('messages.profile_updated'));
    }

    public function wishlist(): View
    {
        $wishlistItems = Wishlist::where('user_id', Auth::id())
            ->with(['product.brand', 'product.approvedOffers.seller'])
            ->latest()
            ->paginate(12);

        return view('account.wishlist', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    public function toggleWishlist(int $productId): RedirectResponse
    {
        $user = Auth::user();
        $exists = Wishlist::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($exists) {
            $exists->delete();
            $message = 'تمت إزالة المنتج من المفضلة.';
        } else {
            Wishlist::create(['user_id' => $user->id, 'product_id' => $productId]);
            $message = 'تمت إضافة المنتج إلى المفضلة.';
        }

        return back()->with('success', $message);
    }

    public function storeReview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'order_item_id' => ['nullable', 'exists:order_items,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $userId = Auth::id();

        // Check if user already reviewed this product
        $alreadyReviewed = ProductReview::where('product_id', $validated['product_id'])
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', __('messages.already_reviewed'));
        }

        // Check verified purchase
        $isVerifiedPurchase = DB::table('order_items')
            ->join('seller_orders', 'order_items.seller_order_id', '=', 'seller_orders.id')
            ->join('parent_orders', 'seller_orders.parent_order_id', '=', 'parent_orders.id')
            ->where('parent_orders.user_id', $userId)
            ->where('order_items.product_id', $validated['product_id'])
            ->whereIn('seller_orders.status', ['delivered', 'completed'])
            ->exists();

        ProductReview::create([
            'product_id' => $validated['product_id'],
            'user_id' => $userId,
            'order_item_id' => $validated['order_item_id'] ?? null,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'],
            'is_verified_purchase' => $isVerifiedPurchase,
            'status' => 'approved',
        ]);

        return back()->with('success', __('messages.review_submitted'));
    }
}
