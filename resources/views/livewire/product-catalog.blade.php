<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ mobileFiltersOpen: false }">
    
    <!-- Breadcrumb & Results Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-stone-200">
        <div>
            <nav class="text-xs text-stone-500 mb-1 flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('storefront.home') }}" class="hover:text-stone-800">الرئيسية</a>
                <span>/</span>
                <span class="text-stone-800 font-semibold">دليل المنتجات</span>
                @if(!empty($category))
                    <span>/</span>
                    <span class="text-amber-600 font-bold">{{ $categories->firstWhere('slug', $category)?->name ?? $category }}</span>
                @endif
                @if(!empty($search))
                    <span>/</span>
                    <span class="text-stone-700">نتائج البحث: "{{ $search }}"</span>
                @endif
            </nav>
            <h1 class="text-xl sm:text-2xl font-black text-stone-900 tracking-tight">
                {{ !empty($search) ? 'نتائج البحث عن "' . $search . '"' : (!empty($category) ? ($categories->firstWhere('slug', $category)?->name ?? 'المنتجات') : 'كافة المنتجات المتاحة') }}
            </h1>
            <p class="text-xs text-stone-500 mt-1">
                {{ __('messages.showing_results', ['count' => $products->total()]) }}
            </p>
        </div>

        <!-- Sort Control & Mobile Filter Toggle -->
        <div class="flex items-center gap-3">
            <button type="button" @click="mobileFiltersOpen = !mobileFiltersOpen" class="lg:hidden flex items-center gap-2 px-3 py-2 rounded-xl border border-stone-300 bg-white text-xs font-bold text-stone-700 shadow-2xs">
                <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                <span>{{ __('messages.filters') }}</span>
            </button>

            <div class="flex items-center gap-2 text-xs">
                <label for="catalog_sort" class="text-stone-500 font-medium hidden sm:inline">{{ __('messages.sort_by') }}:</label>
                <select id="catalog_sort" wire:model.live="sortBy" class="py-2 px-3 rounded-xl border border-stone-300 bg-white text-xs font-semibold text-stone-800 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    <option value="relevance">{{ __('messages.sort_relevance') }}</option>
                    <option value="price_asc">{{ __('messages.sort_price_low') }}</option>
                    <option value="price_desc">{{ __('messages.sort_price_high') }}</option>
                    <option value="newest">{{ __('messages.sort_newest') }}</option>
                    <option value="rating">{{ __('messages.sort_rating') }}</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (Sidebar + Product Grid) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mt-6">
        
        <!-- Desktop Sidebar Filters -->
        <aside class="hidden lg:block space-y-6 text-xs text-stone-700">
            
            <!-- Reset Filters Pill -->
            @if(!empty($search) || !empty($category) || !empty($brand) || $minPrice || $maxPrice || $inStockOnly || $condition !== 'all')
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/60 flex items-center justify-between">
                    <span class="font-bold text-amber-900">فلاتر مفعلة</span>
                    <button type="button" wire:click="resetFilters" class="text-amber-700 hover:text-amber-900 font-bold underline">
                        {{ __('messages.clear_filters') }}
                    </button>
                </div>
            @endif

            <!-- Category Filter -->
            <div class="p-4 rounded-2xl bg-white border border-stone-200 space-y-3 shadow-2xs">
                <h3 class="font-bold text-sm text-stone-900 pb-2 border-b border-stone-100 flex items-center justify-between">
                    <span>{{ __('messages.category') }}</span>
                    @if(!empty($category))
                        <button type="button" wire:click="$set('category', '')" class="text-[10px] text-stone-600 hover:text-stone-800">إلغاء</button>
                    @endif
                </h3>
                <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                    <button type="button" wire:click="$set('category', '')" class="w-full text-start py-1 px-2 rounded-lg font-medium transition-colors {{ empty($category) ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-stone-50 text-stone-600' }}">
                        {{ __('messages.all_categories') }}
                    </button>
                    @foreach($categories as $cat)
                        <button type="button" wire:click="$set('category', '{{ $cat->slug }}')" class="w-full text-start py-1 px-2 rounded-lg font-medium transition-colors {{ $category === $cat->slug ? 'bg-amber-100 text-amber-900 font-bold' : 'hover:bg-stone-50 text-stone-600' }}">
                            {{ $cat->name }}
                        </button>
                        @if($cat->children->isNotEmpty())
                            <div class="pr-3 rtl:pr-3 ltr:pl-3 space-y-1">
                                @foreach($cat->children as $sub)
                                    <button type="button" wire:click="$set('category', '{{ $sub->slug }}')" class="w-full text-start py-0.5 px-2 rounded-md text-[11px] transition-colors {{ $category === $sub->slug ? 'bg-amber-50 text-amber-800 font-bold' : 'hover:bg-stone-50 text-stone-500' }}">
                                        • {{ $sub->name }}
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Price Range Filter -->
            <div class="p-4 rounded-2xl bg-white border border-stone-200 space-y-3 shadow-2xs">
                <h3 class="font-bold text-sm text-stone-900 pb-2 border-b border-stone-100">
                    {{ __('messages.price_range') }}
                </h3>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="catalog_min_price" class="text-[10px] text-stone-600 block mb-1">{{ __('messages.min_price') }}</label>
                        <input id="catalog_min_price" type="number" step="1" placeholder="0" wire:model.live.debounce.400ms="minPrice" class="w-full p-2 rounded-lg border border-stone-300 text-xs text-stone-900 outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label for="catalog_max_price" class="text-[10px] text-stone-600 block mb-1">{{ __('messages.max_price') }}</label>
                        <input id="catalog_max_price" type="number" step="1" placeholder="1500" wire:model.live.debounce.400ms="maxPrice" class="w-full p-2 rounded-lg border border-stone-300 text-xs text-stone-900 outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <!-- Brands Filter -->
            <div class="p-4 rounded-2xl bg-white border border-stone-200 space-y-3 shadow-2xs">
                <h3 class="font-bold text-sm text-stone-900 pb-2 border-b border-stone-100 flex items-center justify-between">
                    <span>{{ __('messages.brand') }}</span>
                    @if(!empty($brand))
                        <button type="button" wire:click="$set('brand', '')" class="text-[10px] text-stone-600 hover:text-stone-800">إلغاء</button>
                    @endif
                </h3>
                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                    @foreach($brands as $b)
                        <label class="flex items-center gap-2 cursor-pointer py-1 px-1 rounded-md hover:bg-stone-50">
                            <input type="radio" name="brand_filter" value="{{ $b->slug }}" wire:model.live="brand" class="text-amber-500 focus:ring-amber-500">
                            <span class="text-xs {{ $brand === $b->slug ? 'font-bold text-stone-900' : 'text-stone-600' }}">{{ $b->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Condition Filter -->
            <div class="p-4 rounded-2xl bg-white border border-stone-200 space-y-2.5 shadow-2xs">
                <h3 class="font-bold text-sm text-stone-900 pb-2 border-b border-stone-100">
                    {{ __('messages.condition') }}
                </h3>
                <div class="space-y-1.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="condition" value="all" wire:model.live="condition" class="text-amber-500">
                        <span>الكل</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="condition" value="new" wire:model.live="condition" class="text-amber-500">
                        <span>{{ __('messages.condition_new') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="condition" value="refurbished" wire:model.live="condition" class="text-amber-500">
                        <span>{{ __('messages.condition_refurbished') }}</span>
                    </label>
                </div>
            </div>

            <!-- In-Stock Toggle -->
            <div class="p-4 rounded-2xl bg-white border border-stone-200 shadow-2xs">
                <label class="flex items-center justify-between cursor-pointer">
                    <span class="font-bold text-xs text-stone-900">المتوفر في المخزون فقط</span>
                    <input type="checkbox" wire:model.live="inStockOnly" class="rounded text-amber-500 focus:ring-amber-500 w-4 h-4">
                </label>
            </div>
        </aside>

        <!-- Product Grid Area -->
        <main class="lg:col-span-3">
            @if($products->isEmpty())
                <div class="p-12 text-center bg-white rounded-3xl border border-stone-200 shadow-xs space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center text-3xl mx-auto">
                        🔍
                    </div>
                    <h3 class="font-bold text-base text-stone-900">{{ __('messages.no_products_found') }}</h3>
                    <p class="text-xs text-stone-500 max-w-sm mx-auto">
                        جرّب إزالة بعض الفلاتر أو البحث بكلمات عامة مثل "آيفون" أو "عطر" أو "سامسونج".
                    </p>
                    <button type="button" wire:click="resetFilters" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold rounded-xl text-xs transition-colors shadow-xs">
                        {{ __('messages.clear_filters') }}
                    </button>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($products as $product)
                        <div class="group flex flex-col bg-white rounded-2xl border border-stone-200/90 hover:border-amber-300 hover:shadow-lg transition-all duration-200 overflow-hidden">
                            <!-- Image Container -->
                            <a href="{{ route('storefront.product', $product->slug) }}" class="relative aspect-square bg-stone-50 overflow-hidden block">
                                @if($product->main_image_url)
                                    <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-stone-300 text-3xl">📦</div>
                                @endif

                                @if($product->bestOffer && $product->bestOffer->hasDiscount())
                                    <span class="absolute top-2.5 start-2.5 bg-red-600 text-white font-black text-[10px] px-2 py-0.5 rounded-md shadow-xs">
                                        -{{ $product->bestOffer->discount_percentage }}%
                                    </span>
                                @endif

                                @if($product->is_featured)
                                    <span class="absolute top-2.5 end-2.5 bg-amber-500 text-stone-950 font-extrabold text-[10px] px-2 py-0.5 rounded-md shadow-xs">
                                        مميز
                                    </span>
                                @endif
                            </a>

                            <!-- Info Container -->
                            <div class="p-4 flex flex-col flex-1 justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-1 text-[11px] text-stone-600">
                                        <span>{{ $product->brand?->name ?? $product->category?->name }}</span>
                                        @if($product->bestOffer && $product->bestOffer->seller)
                                            <span class="truncate max-w-[100px] text-stone-600">{{ $product->bestOffer->seller->store_name }}</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('storefront.product', $product->slug) }}" class="font-bold text-xs sm:text-sm text-stone-900 group-hover:text-amber-600 line-clamp-2 transition-colors">
                                        {{ $product->name }}
                                    </a>
                                </div>

                                <div class="space-y-2.5 pt-2 border-t border-stone-100">
                                    <!-- Price -->
                                    @if($product->bestOffer)
                                        <div class="flex items-baseline gap-2 flex-wrap">
                                            <span class="text-base sm:text-lg font-black text-stone-900">
                                                {{ number_format($product->bestOffer->effective_price, 3) }}
                                            </span>
                                            <span class="text-xs font-bold text-amber-600">{{ __('messages.currency') }}</span>

                                            @if($product->bestOffer->hasDiscount())
                                                <span class="text-xs text-stone-600 line-through">
                                                    {{ number_format($product->bestOffer->price, 3) }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-stone-500 font-medium">غير متوفر حالياً</span>
                                    @endif

                                    <!-- Add to Cart Button -->
                                    @if($product->bestOffer && $product->bestOffer->available_stock > 0)
                                        <livewire:add-to-cart-button
                                            :key="'cat-btn-'.$product->id"
                                            :offerId="$product->bestOffer->id"
                                            :quantity="1"
                                            :buttonClass="'w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-stone-950 font-bold text-xs flex items-center justify-center gap-1.5 shadow-2xs transition-all'"
                                        />
                                    @else
                                        <button disabled class="w-full py-2 px-3 rounded-xl bg-stone-100 text-stone-600 font-medium text-xs cursor-not-allowed">
                                            {{ __('messages.out_of_stock') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </main>
    </div>
</div>
