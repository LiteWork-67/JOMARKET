@extends('layouts.storefront')

@section('title', __('messages.app_name') . ' — سوق الأردن الإلكتروني الموثوق')

@section('content')
<div class="space-y-12 pb-16">

    <!-- Hero Promotional Banner -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-stone-900 via-stone-800 to-amber-950 text-white shadow-xl min-h-[360px] flex items-center">
            <!-- Background Image overlay -->
            <div class="absolute inset-0 opacity-20 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1600&auto=format&fit=crop&q=80')"></div>
            
            <div class="relative z-10 p-8 sm:p-12 max-w-2xl space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-bold">
                    <span>🇯🇴 تسوق من أفضل المتاجر الأردنية المعتمدة</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-black tracking-tight leading-tight">
                    أفضل الأجهزة والأزياء والعطور <span class="text-amber-400">بأسعار لا تُنافس</span>
                </h1>
                <p class="text-stone-300 text-sm sm:text-base leading-relaxed">
                    منصة جوردن مارت تجمع لك آلاف المنتجات الأصلية مع كفالة رسمية، توصيل سريع لكافة محافظات المملكة، وإمكانية الدفع عند الاستلام.
                </p>
                <div class="pt-2 flex items-center gap-3 flex-wrap">
                    <a href="{{ route('storefront.catalog') }}" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-stone-950 font-black text-xs sm:text-sm shadow-lg hover:scale-105 active:scale-95 transition-all">
                        تسوق الآن في العروض
                    </a>
                    <a href="{{ route('storefront.category', 'electronics') }}" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs sm:text-sm backdrop-blur-xs border border-white/10 transition-colors">
                        الهواتف والإلكترونيات
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Top Categories Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">تسوق حسب الفئات</h2>
                <p class="text-xs text-stone-500">اختر الفئة التي تبحث عنها لتصفح آلاف العروض</p>
            </div>
            <a href="{{ route('storefront.catalog') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">عرض كافة الأقسام ←</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach($categories as $cat)
                <a href="{{ route('storefront.category', $cat->slug) }}" class="group p-5 rounded-2xl bg-white border border-stone-200/90 hover:border-amber-400 hover:shadow-md transition-all flex flex-col items-center text-center space-y-3">
                    <div class="w-16 h-16 rounded-2xl bg-amber-50 group-hover:bg-amber-100 text-amber-700 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-2xs">
                        @if($cat->slug === 'electronics') 📱
                        @elseif($cat->slug === 'fashion') 👗
                        @elseif($cat->slug === 'home-kitchen') 🍳
                        @elseif($cat->slug === 'beauty-perfumes') 🌸
                        @else 🏷️ @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-stone-900 group-hover:text-amber-600 transition-colors">{{ $cat->name }}</h3>
                        <p class="text-[11px] text-stone-500 mt-0.5">{{ $cat->children->count() }} تصنيفات فرعية</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Today's Exclusive Deals -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-amber-50 via-white to-orange-50/50 border border-amber-200/60 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-amber-500 text-stone-950 font-black flex items-center justify-center text-lg shadow-xs">🔥</span>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">عروض وتخفيضات اليوم</h2>
                        <p class="text-xs text-stone-500">خصومات حصرية لفترة محدودة على منتجات مختارة</p>
                    </div>
                </div>
                <a href="{{ route('storefront.catalog') }}?deals=1" class="text-xs font-bold text-amber-700 hover:text-amber-800">كل التخفيضات ←</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($deals as $deal)
                    <div class="group bg-white rounded-2xl border border-stone-200 hover:border-amber-400 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden">
                        <a href="{{ route('storefront.product', $deal->product->slug) }}" class="relative aspect-square bg-stone-50 overflow-hidden block">
                            @if($deal->product->main_image_url)
                                <img src="{{ $deal->product->main_image_url }}" alt="{{ $deal->product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">📦</div>
                            @endif
                            <span class="absolute top-2.5 start-2.5 bg-red-600 text-white font-black text-[10px] px-2 py-0.5 rounded-md shadow-xs">
                                -{{ $deal->discount_percentage }}%
                            </span>
                        </a>
                        <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                            <div>
                                <span class="text-[10px] text-stone-600">{{ $deal->seller?->store_name }}</span>
                                <a href="{{ route('storefront.product', $deal->product->slug) }}" class="block font-bold text-xs sm:text-sm text-stone-900 hover:text-amber-600 line-clamp-2">
                                    {{ $deal->product->name }}
                                </a>
                            </div>
                            <div class="pt-2 border-t border-stone-100">
                                <div class="flex items-baseline gap-2">
                                    <span class="font-black text-base sm:text-lg text-stone-900">{{ number_format($deal->effective_price, 3) }}</span>
                                    <span class="text-xs font-bold text-amber-600">د.أ</span>
                                    <span class="text-xs text-stone-600 line-through">{{ number_format($deal->price, 3) }}</span>
                                </div>
                                <div class="mt-2.5">
                                    <livewire:add-to-cart-button
                                        :key="'deal-btn-'.$deal->id"
                                        :offerId="$deal->id"
                                        :quantity="1"
                                        :buttonClass="'w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs'"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">المنتجات الأكثر طلباً</h2>
                <p class="text-xs text-stone-500">أحدث الأجهزة والإكسسوارات الرائجة في الأردن</p>
            </div>
            <a href="{{ route('storefront.catalog') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">تصفح الكل ←</a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 sm:gap-6">
            @foreach($featuredProducts as $item)
                <div class="group bg-white rounded-2xl border border-stone-200 hover:border-amber-400 hover:shadow-lg transition-all flex flex-col justify-between overflow-hidden">
                    <a href="{{ route('storefront.product', $item->slug) }}" class="relative aspect-square bg-stone-50 overflow-hidden block">
                        @if($item->main_image_url)
                            <img src="{{ $item->main_image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-stone-300">📦</div>
                        @endif
                    </a>
                    <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] text-stone-600">{{ $item->brand?->name }}</span>
                            <a href="{{ route('storefront.product', $item->slug) }}" class="block font-bold text-xs sm:text-sm text-stone-900 hover:text-amber-600 line-clamp-2">
                                {{ $item->name }}
                            </a>
                        </div>
                        <div class="pt-2 border-t border-stone-100">
                            @if($item->bestOffer)
                                <div class="flex items-baseline gap-1.5">
                                    <span class="font-black text-base sm:text-lg text-stone-900">{{ number_format($item->bestOffer->effective_price, 3) }}</span>
                                    <span class="text-xs font-bold text-amber-600">د.أ</span>
                                </div>
                                <div class="mt-2.5">
                                    <livewire:add-to-cart-button
                                        :key="'feat-btn-'.$item->id"
                                        :offerId="$item->bestOffer->id"
                                        :quantity="1"
                                        :buttonClass="'w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs'"
                                    />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Verified Jordanian Sellers -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">متاجر أردنية موثوقة</h2>
                <p class="text-xs text-stone-500">تسوق مباشرة من متاجر مرخصة ومسجلة رسمياً في المملكة</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($sellers as $seller)
                <div class="p-6 rounded-2xl bg-white border border-stone-200 hover:border-amber-400 hover:shadow-md transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-800 font-black text-xl flex items-center justify-center shrink-0">
                            🏪
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-base text-stone-900">{{ $seller->store_name }}</h3>
                                <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                    ✓ موثق رسمياً
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 line-clamp-1 mt-1">{{ $seller->description_ar ?: $seller->description_en }}</p>
                            <div class="flex items-center gap-3 text-xs text-stone-600 mt-2">
                                <span class="text-amber-500 font-bold">★ {{ number_format($seller->rating, 1) }} ({{ $seller->reviews_count }} تقييم)</span>
                                <span>•</span>
                                <span>📍 {{ $seller->governorate }}، {{ $seller->city }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('storefront.seller', $seller->slug) }}" class="px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs shrink-0 transition-colors shadow-xs">
                        زيارة المتجر ←
                    </a>
                </div>
            @endforeach
        </div>
    </section>

</div>
@endsection
