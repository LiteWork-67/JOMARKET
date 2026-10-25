<div>
    @if($offer && $offer->available_stock > 0)
        <button
            type="button"
            wire:click="addToCart"
            wire:loading.attr="disabled"
            class="{{ $buttonClass ?: 'w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-stone-950 font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all disabled:opacity-50' }}">
            <span wire:loading.remove wire:target="addToCart">
                @if($isAdded)
                    ✓ تمت الإضافة بنجاح!
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>{{ $isBuyNow ? __('messages.buy_now') : __('messages.add_to_cart') }}</span>
                @endif
            </span>
            <span wire:loading wire:target="addToCart" class="inline-flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>جاري الإضافة...</span>
            </span>
        </button>
    @else
        <button type="button" disabled class="w-full py-3 px-4 rounded-xl bg-stone-200 text-stone-500 font-bold text-xs cursor-not-allowed">
            {{ __('messages.out_of_stock') }}
        </button>
    @endif
</div>
