<div class="space-y-6">
    <!-- Price & Savings Card -->
    <div class="p-5 rounded-2xl bg-stone-50 border border-stone-200/80 shadow-2xs">
        @if($selectedOffer)
            <div class="flex items-baseline gap-3 flex-wrap">
                <span class="text-3xl font-black text-stone-900 tracking-tight">
                    {{ number_format($selectedOffer->effective_price, 3) }}
                </span>
                <span class="text-sm font-bold text-amber-600">{{ __('messages.currency') }}</span>

                @if($selectedOffer->hasDiscount())
                    <span class="text-base text-stone-600 line-through font-medium">
                        {{ number_format($selectedOffer->price, 3) }} {{ __('messages.currency') }}
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-extrabold text-xs">
                        {{ __('messages.save_discount', ['percent' => $selectedOffer->discount_percentage]) }}
                    </span>
                @endif
            </div>

            <!-- Tax & Delivery Info -->
            <p class="text-xs text-stone-500 mt-1.5 flex items-center gap-1.5">
                <span>شامل ضريبة المبيعات الأردنية (16%)</span>
                <span>•</span>
                <span class="text-emerald-700 font-semibold">توصيل قياسي 3.000 د.أ (مجاني فوق 50 د.أ)</span>
            </p>

            <!-- Availability & Handling -->
            <div class="mt-4 pt-4 border-t border-stone-200/60 flex items-center justify-between text-xs">
                @if($selectedOffer->available_stock > 0)
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-bold text-emerald-700">{{ __('messages.in_stock') }}</span>
                        <span class="text-stone-500">({{ $selectedOffer->available_stock }} قطعة متوفرة)</span>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                        <span class="font-bold text-red-700">{{ __('messages.out_of_stock') }}</span>
                    </div>
                @endif

                <span class="text-stone-600 font-medium bg-stone-100 px-2.5 py-1 rounded-lg">
                    {{ __('messages.handling_time') }}
                </span>
            </div>
        @else
            <p class="text-stone-500 text-sm font-medium">لا توجد عروض بيع نشطة حالياً لهذا المنتج.</p>
        @endif
    </div>

    <!-- Seller Information & Verification Pill -->
    @if($selectedOffer && $selectedOffer->seller)
        <div class="p-4 rounded-xl bg-white border border-stone-200 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 font-black flex items-center justify-center text-sm shrink-0">
                    🏪
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-stone-500">{{ __('messages.sold_by') }}:</span>
                        <a href="{{ route('storefront.seller', $selectedOffer->seller->slug) }}" class="font-bold text-xs text-stone-900 hover:text-amber-600 transition-colors">
                            {{ $selectedOffer->seller->store_name }}
                        </a>
                        <span class="inline-flex items-center gap-1 text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-1.5 py-0.5 rounded-full font-bold">
                            ✓ موثق
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-stone-500 mt-0.5">
                        <span class="text-amber-500 font-bold">★ {{ number_format($selectedOffer->seller->rating, 1) }}</span>
                        <span>•</span>
                        <span>{{ $selectedOffer->seller->city }}، الأردن</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('storefront.seller', $selectedOffer->seller->slug) }}" class="text-xs font-bold text-stone-700 hover:text-amber-600 px-3 py-1.5 rounded-lg border border-stone-200 hover:bg-stone-50 transition-colors shrink-0">
                {{ __('messages.visit_store') }}
            </a>
        </div>
    @endif

    <!-- Alternative Seller Offers (Multi-Vendor Marketplace) -->
    @if($product->offers->count() > 1)
        <div class="space-y-2">
            <h4 class="font-bold text-xs text-stone-900">عروض متوفرة من بائعين آخرين في الأردن:</h4>
            <div class="space-y-2">
                @foreach($product->offers as $offer)
                    <div wire:click="selectOffer({{ $offer->id }})" class="p-3 rounded-xl border cursor-pointer transition-all flex items-center justify-between {{ $selectedOfferId === $offer->id ? 'border-amber-500 bg-amber-50/40 ring-1 ring-amber-500/20' : 'border-stone-200 hover:border-stone-300 bg-white' }}">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="offer_selection" value="{{ $offer->id }}" {{ $selectedOfferId === $offer->id ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-500">
                            <div>
                                <p class="font-bold text-xs text-stone-900">{{ $offer->seller?->store_name }}</p>
                                <p class="text-[10px] text-stone-500">الحالة: {{ $offer->condition === 'new' ? 'جديد أصلية' : $offer->condition }} • متوفر: {{ $offer->available_stock }}</p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="font-black text-xs text-stone-900">{{ number_format($offer->effective_price, 3) }}</span>
                            <span class="text-[10px] text-amber-600 font-bold">{{ __('messages.currency') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quantity & Action Buttons -->
    @if($selectedOffer && $selectedOffer->available_stock > 0)
        <div class="space-y-3 pt-2">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-stone-700">الكمية:</span>
                <div class="flex items-center rounded-xl border border-stone-300 bg-white overflow-hidden shadow-2xs">
                    <button type="button" wire:click="decrementQty" class="px-3 py-1.5 text-stone-600 hover:bg-stone-100 font-bold text-sm">-</button>
                    <span class="px-4 py-1.5 text-xs font-bold text-stone-900 min-w-[36px] text-center">{{ $quantity }}</span>
                    <button type="button" wire:click="incrementQty" class="px-3 py-1.5 text-stone-600 hover:bg-stone-100 font-bold text-sm">+</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <livewire:add-to-cart-button
                    :key="'cart-'.$selectedOffer->id.'-'.$quantity"
                    :offerId="$selectedOffer->id"
                    :quantity="$quantity"
                    :buttonClass="'w-full py-3.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-stone-950 font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all'"
                />
                <button type="button" class="w-full py-3.5 px-4 rounded-xl bg-stone-900 hover:bg-stone-800 active:scale-[0.99] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all">
                    <span>⚡ {{ __('messages.buy_now') }}</span>
                </button>
            </div>
        </div>
    @endif
</div>
