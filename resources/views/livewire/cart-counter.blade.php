<a href="{{ route('cart.index') }}" class="relative flex items-center gap-2 p-2 rounded-xl bg-stone-100 hover:bg-amber-100 hover:text-amber-900 text-stone-800 transition-colors shrink-0 group">
    <div class="relative">
        <svg class="w-6 h-6 text-stone-700 group-hover:text-amber-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        @if($count > 0)
            <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white font-black text-[10px] w-5 h-5 rounded-full flex items-center justify-center shadow-xs animate-pulse">
                {{ $count }}
            </span>
        @endif
    </div>
    <div class="hidden sm:flex flex-col text-start leading-none">
        <span class="text-[10px] text-stone-500">{{ __('messages.cart') }}</span>
        <span class="text-xs font-bold">{{ $count }} {{ __('messages.items_count', ['count' => '']) }}</span>
    </div>
</a>
