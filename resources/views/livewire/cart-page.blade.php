<div class="py-8 bg-stone-50 min-h-[75vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Title -->
        <div class="mb-6">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-2">
                <a href="{{ route('storefront.home') }}" class="hover:text-stone-900 transition-colors">{{ __('messages.app_name') }}</a>
                <span>/</span>
                <span class="text-stone-900 font-semibold">{{ __('messages.shopping_cart') }}</span>
            </nav>
            <h1 class="text-2xl font-black text-stone-900 tracking-tight flex items-center gap-2">
                <span>{{ __('messages.shopping_cart') }}</span>
                @if($cart->items->count() > 0)
                    <span class="text-sm font-bold px-2.5 py-0.5 rounded-full bg-stone-200 text-stone-800">
                        {{ $cart->items->sum('quantity') }}
                    </span>
                @endif
            </h1>
        </div>

        @if($cart->items->isEmpty())
            <!-- Empty Cart State -->
            <div class="bg-white rounded-2xl border border-stone-200 p-12 text-center max-w-xl mx-auto shadow-xs">
                <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-black text-stone-900 mb-2">{{ __('messages.empty_cart') }}</h2>
                <p class="text-xs text-stone-500 mb-6 leading-relaxed">{{ __('messages.empty_cart_hint') }}</p>
                <a href="{{ route('storefront.catalog') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold text-xs shadow-sm transition-all">
                    <span>{{ __('messages.start_shopping') }}</span>
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        @else
            <!-- Active Cart with Items -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Items List (Grouped by Seller) -->
                <div class="lg:col-span-2 space-y-6">
                    @foreach($sellerGroups as $sellerId => $items)
                        @php
                            $seller = $items->first()->offer->seller;
                        @endphp
                        <div class="bg-white rounded-2xl border border-stone-200 shadow-xs overflow-hidden">
                            <!-- Seller Header / Fulfillment Group -->
                            <div class="bg-stone-50/80 px-5 py-3 border-b border-stone-200 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 flex items-center justify-center text-xs font-black">
                                        {{ Str::substr($seller->store_name_ar ?? $seller->store_name_en, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black text-stone-900">
                                                {{ app()->getLocale() === 'ar' ? $seller->store_name_ar : $seller->store_name_en }}
                                            </span>
                                            @if($seller->status === 'approved')
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                                    ✓ {{ __('messages.verified_seller') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-stone-500">
                                            {{ __('messages.seller_shipment', ['seller' => app()->getLocale() === 'ar' ? $seller->store_name_ar : $seller->store_name_en]) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold text-stone-500">
                                    {{ $items->count() }} {{ __('messages.items_count', ['count' => '']) }}
                                </span>
                            </div>

                            <!-- Items in this shipment -->
                            <div class="divide-y divide-stone-100 p-5 space-y-4">
                                @foreach($items as $item)
                                    @php
                                        $offer = $item->offer;
                                        $product = $offer->product;
                                    @endphp
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pt-4 first:pt-0">
                                        <!-- Product Image & Title -->
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="w-16 h-16 rounded-xl bg-stone-100 border border-stone-200 overflow-hidden shrink-0 flex items-center justify-center">
                                                @if($product->primary_image_url)
                                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->title_ar }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xs font-black text-stone-400">JM</span>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <a href="{{ route('storefront.product', $product->slug) }}" class="text-xs font-bold text-stone-900 hover:text-red-600 transition-colors line-clamp-2">
                                                    {{ app()->getLocale() === 'ar' ? $product->title_ar : $product->title_en }}
                                                </a>
                                                <div class="flex items-center gap-2 mt-1 text-[11px] text-stone-500">
                                                    <span>{{ __('messages.sku') }}: <span class="font-mono">{{ $offer->sku }}</span></span>
                                                    @if($offer->available_stock <= 3)
                                                        <span class="text-red-600 font-bold">بقي {{ $offer->available_stock }} فقط!</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quantity Controls & Price -->
                                        <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                            <!-- Quantity Selector -->
                                            <div class="flex items-center border border-stone-200 rounded-xl bg-white shadow-2xs">
                                                <button type="button" wire:click="decrementQuantity({{ $item->id }})" class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-100 rounded-s-xl transition-colors">
                                                    -
                                                </button>
                                                <span class="w-9 text-center text-xs font-bold text-stone-900">
                                                    {{ $item->quantity }}
                                                </span>
                                                <button type="button" wire:click="incrementQuantity({{ $item->id }})" 
                                                    @if($item->quantity >= $offer->available_stock) disabled @endif
                                                    class="w-8 h-8 flex items-center justify-center text-stone-600 hover:bg-stone-100 rounded-e-xl transition-colors disabled:opacity-40">
                                                    +
                                                </button>
                                            </div>

                                            <!-- Unit / Subtotal Price -->
                                            <div class="text-end min-w-[90px]">
                                                <div class="text-sm font-black text-stone-950">
                                                    {{ number_format((float) $item->unit_price * $item->quantity, 3) }}
                                                    <span class="text-[10px] font-normal text-stone-600">{{ __('messages.currency') }}</span>
                                                </div>
                                                <div class="text-[10px] text-stone-400">
                                                    {{ number_format((float) $item->unit_price, 3) }} {{ __('messages.currency') }} / قطعة
                                                </div>
                                            </div>

                                            <!-- Remove Action -->
                                            <button type="button" wire:click="removeItem({{ $item->id }})" title="{{ __('messages.remove_item') }}" class="p-1.5 text-stone-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- Free Delivery Progress Bar -->
                    @if($totals['subtotal'] < 50.000)
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-200 text-stone-800 text-xs flex items-center gap-3">
                            <span class="text-xl">🚚</span>
                            <div>
                                <p class="font-bold">
                                    أضف منتجات بقيمة 
                                    <span class="text-red-600 font-black">{{ number_format(50.000 - $totals['subtotal'], 3) }} {{ __('messages.currency') }}</span> 
                                    لتحصل على شحن مجاني لكافة محافظات الأردن!
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                            <span class="text-xl">🎉</span>
                            <p class="font-bold">تهانينا! طلبيتك مؤهلة للشحن المجاني إلى أي مكان في المملكة.</p>
                        </div>
                    @endif
                </div>

                <!-- Order Summary Sidebar (1/3) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-5">
                        <h2 class="text-sm font-black text-stone-900 border-b border-stone-100 pb-3">
                            {{ __('messages.order_summary') }}
                        </h2>

                        <!-- Price Breakdown -->
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between text-stone-600">
                                <span>{{ __('messages.subtotal') }}</span>
                                <span class="font-bold text-stone-900">{{ number_format($totals['subtotal'], 3) }} {{ __('messages.currency') }}</span>
                            </div>

                            <div class="flex justify-between text-stone-600">
                                <span>{{ __('messages.shipping_fee') }}</span>
                                @if($totals['shipping'] == 0.000)
                                    <span class="font-bold text-emerald-600">مجاناً</span>
                                @else
                                    <span class="font-bold text-stone-900">{{ number_format($totals['shipping'], 3) }} {{ __('messages.currency') }}</span>
                                @endif
                            </div>

                            @if($totals['discount'] > 0)
                                <div class="flex justify-between text-emerald-600 font-semibold">
                                    <span>{{ __('messages.discount') }} ({{ $appliedCoupon->code }})</span>
                                    <span>-{{ number_format($totals['discount'], 3) }} {{ __('messages.currency') }}</span>
                                </div>
                            @endif

                            <div class="pt-3 border-t border-stone-200 flex justify-between items-baseline">
                                <span class="text-sm font-black text-stone-900">{{ __('messages.total') }}</span>
                                <div class="text-end">
                                    <span class="text-xl font-black text-stone-950">{{ number_format($totals['total'], 3) }}</span>
                                    <span class="text-xs font-bold text-stone-600">{{ __('messages.currency') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Coupon Input -->
                        <div class="pt-4 border-t border-stone-100">
                            @if($appliedCoupon)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-emerald-800">{{ $appliedCoupon->code }}</span>
                                        <span class="text-[10px] text-emerald-600 font-semibold">خصم مفعّل</span>
                                    </div>
                                    <button type="button" wire:click="removeCoupon" class="text-xs text-red-600 hover:text-red-700 font-bold">
                                        {{ __('messages.remove_coupon') }}
                                    </button>
                                </div>
                            @else
                                <form wire:submit.prevent="applyCoupon" class="space-y-2">
                                    <div class="flex gap-2">
                                        <input type="text" wire:model="couponCode" placeholder="{{ __('messages.coupon_code') }}" class="w-full text-xs rounded-xl border border-stone-300 px-3 py-2 uppercase font-mono tracking-wider focus:border-stone-900 focus:ring-1 focus:ring-stone-900">
                                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs shrink-0 transition-colors">
                                            {{ __('messages.apply_coupon') }}
                                        </button>
                                    </div>
                                    @if($couponError)
                                        <p class="text-[11px] text-red-600 font-medium">{{ $couponError }}</p>
                                    @endif
                                    @if($couponMessage)
                                        <p class="text-[11px] text-emerald-600 font-medium">{{ $couponMessage }}</p>
                                    @endif
                                </form>
                            @endif
                        </div>

                        <!-- Proceed to Checkout Button -->
                        <a href="{{ route('checkout.index') }}" class="w-full py-3.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-black text-sm shadow-sm hover:shadow transition-all flex items-center justify-center gap-2">
                            <span>{{ __('messages.proceed_to_checkout') }}</span>
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>

                        <!-- Continue Shopping Link -->
                        <div class="text-center pt-2">
                            <a href="{{ route('storefront.catalog') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-900 transition-colors">
                                ← {{ __('messages.continue_shopping') }}
                            </a>
                        </div>
                    </div>

                    <!-- Trust Signals Box -->
                    <div class="bg-white rounded-2xl border border-stone-200 p-5 space-y-3 text-xs text-stone-600 shadow-2xs">
                        <div class="flex items-center gap-3">
                            <span class="text-base text-emerald-600">🛡️</span>
                            <span class="font-medium">تسوق آمن وحماية كاملة للمشتري</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-base text-amber-500">💵</span>
                            <span class="font-medium">الدفع نقداً عند الاستلام أو فوراً عبر كليك (CliQ)</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-base text-blue-500">🔄</span>
                            <span class="font-medium">إمكانية الاسترجاع والتبديل وفق سياسة المنصة</span>
                        </div>
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
