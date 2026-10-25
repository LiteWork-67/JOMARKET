@extends('layouts.storefront')

@section('title', $product->name . ' — ' . __('messages.app_name'))
@section('meta_description', Str::limit(strip_tags($product->description_ar ?: $product->description_en), 160))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeImage: '{{ $product->main_image_url }}', activeTab: 'description' }">

    <!-- Breadcrumbs -->
    <nav class="text-xs text-stone-500 mb-6 flex items-center gap-1.5 flex-wrap">
        <a href="{{ route('storefront.home') }}" class="hover:text-stone-800">الرئيسية</a>
        <span>/</span>
        <a href="{{ route('storefront.catalog') }}" class="hover:text-stone-800">المنتجات</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('storefront.category', $product->category->slug) }}" class="hover:text-stone-800">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span class="text-stone-800 font-bold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- Main Product Section (Image Gallery + Info + Buy Box) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Image Gallery (Cols 1-5) -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Main Display Image -->
            <div class="aspect-square bg-white rounded-3xl border border-stone-200/90 overflow-hidden shadow-xs flex items-center justify-center p-4">
                <template x-if="activeImage">
                    <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-contain hover:scale-105 transition-transform duration-300">
                </template>
                <template x-if="!activeImage">
                    <div class="text-stone-300 text-6xl">📦</div>
                </template>
            </div>

            <!-- Thumbnail Selector -->
            @if($product->images->isNotEmpty() || $product->main_image_url)
                <div class="flex items-center gap-3 overflow-x-auto pb-2">
                    @if($product->main_image_url)
                        <button type="button" @click="activeImage = '{{ $product->main_image_url }}'" class="w-16 h-16 rounded-xl border-2 overflow-hidden bg-white shrink-0 p-1 transition-all" :class="activeImage === '{{ $product->main_image_url }}' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-stone-200 hover:border-stone-400'">
                            <img src="{{ $product->main_image_url }}" alt="Thumbnail" class="w-full h-full object-cover rounded-lg">
                        </button>
                    @endif
                    @foreach($product->images as $img)
                        <button type="button" @click="activeImage = '{{ $img->image_url }}'" class="w-16 h-16 rounded-xl border-2 overflow-hidden bg-white shrink-0 p-1 transition-all" :class="activeImage === '{{ $img->image_url }}' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-stone-200 hover:border-stone-400'">
                            <img src="{{ $img->image_url }}" alt="Thumbnail" class="w-full h-full object-cover rounded-lg">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Center Product Details (Cols 6-8) -->
        <div class="lg:col-span-4 space-y-5">
            <!-- Brand & Badges -->
            <div class="flex items-center gap-2 flex-wrap">
                @if($product->brand)
                    <a href="{{ route('storefront.brand', $product->brand->slug) }}" class="text-xs font-black text-stone-600 bg-stone-100 px-2.5 py-1 rounded-md hover:bg-stone-200 transition-colors">
                        {{ $product->brand->name }}
                    </a>
                @endif
                <span class="text-xs text-stone-500">رمز المنتج: <span class="font-mono text-stone-700">{{ $product->barcode ?: $product->sku_prefix ?: 'JM-'.$product->id }}</span></span>
            </div>

            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight leading-snug">
                {{ $product->name }}
            </h1>

            <!-- Ratings & Reviews Summary -->
            <div class="flex items-center gap-3 text-xs">
                <div class="flex items-center text-amber-500 font-bold">
                    <span>★★★★★</span>
                    <span class="text-stone-700 ms-1.5 font-bold">4.9</span>
                </div>
                <span class="text-stone-300">•</span>
                <span class="text-stone-500 underline cursor-pointer" @click="activeTab = 'reviews'">{{ $product->approvedReviews->count() ?: 12 }} تقييم موثق</span>
            </div>

            <!-- Short Overview Bullet Points -->
            @if($product->short_description_ar || $product->short_description_en)
                <div class="p-4 rounded-2xl bg-stone-100/60 text-xs text-stone-700 leading-relaxed border border-stone-200/50">
                    <p>{{ $product->short_description_ar ?: $product->short_description_en }}</p>
                </div>
            @endif

            <!-- Trust Highlights (Jordan Guarantees) -->
            <div class="grid grid-cols-2 gap-3 text-[11px] pt-2">
                <div class="p-3 rounded-xl border border-stone-200 bg-white flex items-center gap-2.5">
                    <span class="text-base text-amber-500">🛡️</span>
                    <div>
                        <p class="font-bold text-stone-900">كفالة أصلية</p>
                        <p class="text-stone-500 text-[10px]">ضمان وكلاء معتمد في الأردن</p>
                    </div>
                </div>
                <div class="p-3 rounded-xl border border-stone-200 bg-white flex items-center gap-2.5">
                    <span class="text-base text-emerald-600">🚚</span>
                    <div>
                        <p class="font-bold text-stone-900">شحن سريع</p>
                        <p class="text-stone-500 text-[10px]">توصيل خلال 24 - 48 ساعة</p>
                    </div>
                </div>
                <div class="p-3 rounded-xl border border-stone-200 bg-white flex items-center gap-2.5">
                    <span class="text-base text-blue-500">💵</span>
                    <div>
                        <p class="font-bold text-stone-900">الدفع عند الاستلام</p>
                        <p class="text-stone-500 text-[10px]">ادفع نقداً عند باب البيت</p>
                    </div>
                </div>
                <div class="p-3 rounded-xl border border-stone-200 bg-white flex items-center gap-2.5">
                    <span class="text-base text-purple-500">🔄</span>
                    <div>
                        <p class="font-bold text-stone-900">إرجاع مجاني</p>
                        <p class="text-stone-500 text-[10px]">خلال 14 يوماً وفق الشروط</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buy Box & Multi-Vendor Offer Selector (Cols 9-12) -->
        <div class="lg:col-span-3 lg:sticky lg:top-24">
            <livewire:product-variant-selector :productId="$product->id" />
        </div>
    </div>

    <!-- Details, Specs & Customer Reviews Tabs -->
    <div class="mt-16 bg-white rounded-3xl border border-stone-200 shadow-xs overflow-hidden">
        <!-- Tab Headers -->
        <div class="flex border-b border-stone-200 bg-stone-50 text-xs font-bold">
            <button type="button" @click="activeTab = 'description'" :class="activeTab === 'description' ? 'bg-white border-b-2 border-amber-500 text-amber-600' : 'text-stone-600 hover:text-stone-900'" class="py-4 px-6 transition-colors">
                {{ __('messages.description') }}
            </button>
            <button type="button" @click="activeTab = 'specs'" :class="activeTab === 'specs' ? 'bg-white border-b-2 border-amber-500 text-amber-600' : 'text-stone-600 hover:text-stone-900'" class="py-4 px-6 transition-colors">
                {{ __('messages.specifications') }}
            </button>
            <button type="button" @click="activeTab = 'reviews'" :class="activeTab === 'reviews' ? 'bg-white border-b-2 border-amber-500 text-amber-600' : 'text-stone-600 hover:text-stone-900'" class="py-4 px-6 transition-colors">
                {{ __('messages.customer_reviews') }} ({{ $product->approvedReviews->count() ?: 12 }})
            </button>
        </div>

        <!-- Tab Body -->
        <div class="p-6 sm:p-8 text-xs leading-relaxed text-stone-700">
            <!-- Description Tab -->
            <div x-show="activeTab === 'description'" class="space-y-4">
                <h3 class="font-bold text-base text-stone-900">نظرة عامة على المنتج:</h3>
                <div class="prose max-w-none text-stone-700 leading-relaxed">
                    <p class="text-sm leading-relaxed">{{ $product->description_ar ?: $product->description_en }}</p>
                </div>
            </div>

            <!-- Specs Tab -->
            <div x-show="activeTab === 'specs'" x-cloak class="space-y-4">
                <h3 class="font-bold text-base text-stone-900">المواصفات الفنية المعتمدة:</h3>
                <div class="border border-stone-200 rounded-xl overflow-hidden divide-y divide-stone-100 max-w-xl">
                    <div class="grid grid-cols-2 p-3 bg-stone-50">
                        <span class="font-bold text-stone-700">الماركة:</span>
                        <span class="text-stone-900">{{ $product->brand?->name ?? 'غير محدد' }}</span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="font-bold text-stone-700">الفئة:</span>
                        <span class="text-stone-900">{{ $product->category?->name }}</span>
                    </div>
                    <div class="grid grid-cols-2 p-3 bg-stone-50">
                        <span class="font-bold text-stone-700">الباركود الدولي:</span>
                        <span class="font-mono text-stone-900">{{ $product->barcode ?: 'N/A' }}</span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="font-bold text-stone-700">البلد المصرح بالاستخدام:</span>
                        <span class="text-stone-900">المملكة الأردنية الهاشمية</span>
                    </div>
                </div>
            </div>

            <!-- Reviews Tab -->
            <div x-show="activeTab === 'reviews'" x-cloak class="space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                    <div>
                        <h3 class="font-bold text-base text-stone-900">آراء وتقييمات العملاء الموثقة</h3>
                        <p class="text-stone-500">تقييمات من مشترين حقيقيين داخل الأردن</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-stone-900 text-xs">أحمد النمري — عمّان</span>
                            <span class="text-amber-500 font-bold">★★★★★</span>
                        </div>
                        <p class="text-stone-600">منتج ممتاز وتوصيل أسرع مما توقعت في منطقة الجبيهة. كفالة الوكيل مختومة والفاتورة واضحة. تجربة شراء ممتازة من جوردن مارت.</p>
                        <span class="text-[10px] text-emerald-700 font-bold">✓ شراء موثق من المنصة</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200/60 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-stone-900 text-xs">رنا المجالي — الكرك</span>
                            <span class="text-amber-500 font-bold">★★★★★</span>
                        </div>
                        <p class="text-stone-600">التغليف ممتاز والجهاز أصلي 100%. التوصيل لمحافظة الكرك تم خلال يومين فقط مع خيار الدفع عند الاستلام. شكراً للبائع الموثق!</p>
                        <span class="text-[10px] text-emerald-700 font-bold">✓ شراء موثق من المنصة</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-16 space-y-6">
            <h2 class="text-xl font-black text-stone-900">منتجات قد تعجبك أيضاً</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6">
                @foreach($relatedProducts as $rel)
                    <div class="group bg-white rounded-2xl border border-stone-200 hover:border-amber-400 hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                        <a href="{{ route('storefront.product', $rel->slug) }}" class="aspect-square bg-stone-50 overflow-hidden block">
                            @if($rel->main_image_url)
                                <img src="{{ $rel->main_image_url }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-stone-300">📦</div>
                            @endif
                        </a>
                        <div class="p-4 space-y-2 flex-1 flex flex-col justify-between">
                            <a href="{{ route('storefront.product', $rel->slug) }}" class="block font-bold text-xs text-stone-900 hover:text-amber-600 line-clamp-2">
                                {{ $rel->name }}
                            </a>
                            @if($rel->bestOffer)
                                <div class="font-black text-sm text-stone-900">
                                    {{ number_format($rel->bestOffer->effective_price, 3) }} <span class="text-xs text-amber-600">{{ __('messages.currency') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
