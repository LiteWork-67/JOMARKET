@extends('account.layout')

@section('title', __('messages.wishlist') . ' — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-6">
    <div class="border-b border-stone-100 pb-4">
        <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
            <span>❤️</span>
            <span>{{ __('messages.wishlist') }}</span>
        </h2>
    </div>

    @if($wishlistItems->isEmpty())
        <div class="text-center py-12 text-xs text-stone-500 max-w-sm mx-auto">
            <span class="text-3xl block mb-2">💔</span>
            <p class="font-bold text-stone-800 mb-1">قائمة المفضلة فارغة</p>
            <p class="mb-4">احفظ المنتجات التي تعجبك هنا لتتمكن من الرجوع إليها وشرائها لاحقاً.</p>
            <a href="{{ route('storefront.catalog') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold transition-colors">
                {{ __('messages.start_shopping') }}
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($wishlistItems as $item)
                @php
                    $product = $item->product;
                    $firstOffer = $product->approvedOffers->first();
                @endphp
                <div class="p-4 rounded-2xl border border-stone-200 hover:border-stone-300 transition-all flex flex-col justify-between text-xs space-y-3 bg-white">
                    <div class="flex items-start gap-3">
                        <div class="w-16 h-16 rounded-xl bg-stone-100 border border-stone-200 shrink-0 overflow-hidden flex items-center justify-center">
                            @if($product->primary_image_url)
                                <img src="{{ $product->primary_image_url }}" alt="" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs font-black text-stone-400">JM</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] text-stone-400 block">{{ $product->brand?->name_ar ?? '' }}</span>
                            <a href="{{ route('storefront.product', $product->slug) }}" class="font-bold text-stone-900 hover:text-red-600 line-clamp-2 block">
                                {{ app()->getLocale() === 'ar' ? $product->title_ar : $product->title_en }}
                            </a>
                            @if($firstOffer)
                                <span class="font-black text-stone-950 text-sm mt-1 block">
                                    {{ number_format($firstOffer->effective_price, 3) }} {{ __('messages.currency') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-stone-100 flex items-center justify-between">
                        <form method="POST" action="{{ route('account.wishlist.toggle', $product->id) }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">
                                إزالة من المفضلة
                            </button>
                        </form>

                        <a href="{{ route('storefront.product', $product->slug) }}" class="px-3 py-1.5 rounded-lg bg-stone-900 hover:bg-stone-800 text-white font-bold text-[11px]">
                            عرض المنتج
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-4">
            {{ $wishlistItems->links() }}
        </div>
    @endif
</div>
@endsection
