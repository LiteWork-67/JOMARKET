@extends('layouts.storefront')

@section('title', $seller->store_name . ' — متجر موثق على ' . __('messages.app_name'))

@section('content')
<div class="space-y-10 pb-16">

    <!-- Seller Store Header & Banner -->
    <div class="bg-gradient-to-r from-stone-900 via-stone-800 to-amber-950 text-white border-b border-stone-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-2xl bg-white text-stone-900 shadow-xl flex items-center justify-center text-3xl font-black shrink-0 border-2 border-amber-400">
                        🏪
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-black text-white">{{ $seller->store_name }}</h1>
                            <span class="inline-flex items-center gap-1 text-xs bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 px-2.5 py-0.5 rounded-full font-bold">
                                ✓ تاجر موثق رسمياً
                            </span>
                        </div>
                        <p class="text-xs text-stone-300 max-w-xl">{{ $seller->description_ar ?: $seller->description_en }}</p>
                        <div class="flex items-center gap-4 text-xs text-stone-400 pt-1 flex-wrap">
                            <span class="text-amber-400 font-bold">★ {{ number_format($seller->rating, 1) }} ({{ $seller->reviews_count }} تقييم)</span>
                            <span>•</span>
                            <span>📍 {{ $seller->governorate }}، {{ $seller->city }}</span>
                            @if($seller->commercial_registration_no)
                                <span>•</span>
                                <span>سجل تجاري: {{ $seller->commercial_registration_no }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="p-3 rounded-xl bg-white/10 backdrop-blur-xs border border-white/10 text-center min-w-[100px]">
                        <span class="block text-lg font-black text-white">{{ $offers->total() }}</span>
                        <span class="text-[10px] text-stone-400">منتجات معروضة</span>
                    </div>
                    <div class="p-3 rounded-xl bg-white/10 backdrop-blur-xs border border-white/10 text-center min-w-[100px]">
                        <span class="block text-lg font-black text-emerald-400">100%</span>
                        <span class="text-[10px] text-stone-400">كفالة أردنية</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Catalog Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between pb-4 border-b border-stone-200 mb-6">
            <h2 class="text-lg font-black text-stone-900">منتجات وعروض متجر {{ $seller->store_name }}</h2>
            <span class="text-xs text-stone-500">عرض {{ $offers->count() }} من أصل {{ $offers->total() }} منتج</span>
        </div>

        @if($offers->isEmpty())
            <div class="p-12 text-center bg-white rounded-3xl border border-stone-200 shadow-xs">
                <p class="text-stone-500 text-sm font-medium">لا توجد عروض بيع نشطة حالياً لهذا المتجر.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($offers as $offer)
                    <div class="group bg-white rounded-2xl border border-stone-200 hover:border-amber-400 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden">
                        <a href="{{ route('storefront.product', $offer->product->slug) }}" class="relative aspect-square bg-stone-50 overflow-hidden block">
                            @if($offer->product->main_image_url)
                                <img src="{{ $offer->product->main_image_url }}" alt="{{ $offer->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">📦</div>
                            @endif

                            @if($offer->hasDiscount())
                                <span class="absolute top-2.5 start-2.5 bg-red-600 text-white font-black text-[10px] px-2 py-0.5 rounded-md shadow-xs">
                                    -{{ $offer->discount_percentage }}%
                                </span>
                            @endif
                        </a>
                        <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] text-stone-600">{{ $offer->product->brand?->name }}</span>
                                <a href="{{ route('storefront.product', $offer->product->slug) }}" class="block font-bold text-xs sm:text-sm text-stone-900 hover:text-amber-600 line-clamp-2">
                                    {{ $offer->product->name }}
                                </a>
                            </div>
                            <div class="pt-2 border-t border-stone-100">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="font-black text-base sm:text-lg text-stone-900">{{ number_format($offer->effective_price, 3) }}</span>
                                    <span class="text-xs font-bold text-amber-600">د.أ</span>
                                    @if($offer->hasDiscount())
                                        <span class="text-xs text-stone-600 line-through">{{ number_format($offer->price, 3) }}</span>
                                    @endif
                                </div>
                                <div class="mt-2.5">
                                    <livewire:add-to-cart-button
                                        :key="'store-btn-'.$offer->id"
                                        :offerId="$offer->id"
                                        :quantity="1"
                                        :buttonClass="'w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs'"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $offers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
