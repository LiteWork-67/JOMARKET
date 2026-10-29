<!DOCTYPE html>
<html lang="{{ $currentLocale ?? app()->getLocale() }}" dir="{{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('messages.app_name') . ' — ' . __('messages.tagline'))</title>
    <meta name="description" content="@yield('meta_description', __('messages.tagline'))">

    <!-- Fonts: Cairo for Arabic RTL, Instrument Sans for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: {{ ($isRtl ?? (app()->getLocale() === 'ar')) ? "'Cairo', sans-serif" : "'Instrument Sans', sans-serif" }};
        }
    </style>
</head>
<body class="min-h-full flex flex-col bg-stone-50 text-stone-900 antialiased selection:bg-amber-500 selection:text-white" x-data="{ mobileMenuOpen: false, locationModalOpen: false, selectedCity: 'عمّان، الأردن' }">

    <!-- Top Announcement Bar -->
    <div class="bg-stone-900 text-stone-100 text-xs py-2 px-4 border-b border-stone-800">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-4 rtl:space-x-reverse overflow-hidden whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 font-medium text-amber-400">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zm.75 4.75a.75.75 0 00-1.5 0v5.5a.75.75 0 001.5 0v-5.5zm-.75 8.25a1 1 0 100-2 1 1 0 000 2z"/>
                    </svg>
                    <span>{{ __('messages.free_delivery_notice') }}</span>
                </span>
            </div>
            <div class="hidden sm:flex items-center space-x-6 rtl:space-x-reverse shrink-0">
                <span class="text-stone-300 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('messages.cash_on_delivery') }}
                </span>
                <span class="text-stone-300 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                    {{ __('messages.original_guaranteed') }}
                </span>
                <a href="{{ route('locale.set', ($currentLocale ?? 'ar') === 'ar' ? 'en' : 'ar') }}" class="font-bold text-amber-400 hover:text-amber-300 transition-colors">
                    {{ ($currentLocale ?? 'ar') === 'ar' ? 'English (EN)' : 'العربية (AR)' }}
                </a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="sticky top-0 z-40 bg-white border-b border-stone-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
            <div class="flex items-center justify-between gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3 shrink-0">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg text-stone-600 hover:bg-stone-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <a href="{{ route('storefront.home') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-red-600 via-stone-900 to-emerald-700 flex items-center justify-center text-white shadow-sm ring-2 ring-stone-900/5 group-hover:scale-105 transition-transform">
                            <span class="font-black text-xl tracking-tighter">JM</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-tight text-stone-900 leading-none">
                                Jordan<span class="text-red-600">Mart</span>
                            </span>
                            <span class="text-[10px] font-semibold text-stone-500 tracking-wider">سوق الأردن</span>
                        </div>
                    </a>
                </div>

                <!-- Location Selector (Jordan Cities) -->
                <button type="button" @click="locationModalOpen = true" class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-lg border border-stone-200 hover:border-stone-300 hover:bg-stone-50 text-xs text-stone-600 transition-all shrink-0">
                    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <div class="flex flex-col text-start leading-tight">
                        <span class="text-[10px] text-stone-600">{{ __('messages.deliver_to') }}</span>
                        <span class="font-bold text-stone-800" x-text="selectedCity">عمّان، الأردن</span>
                    </div>
                </button>

                <!-- Prominent Livewire Search Bar -->
                <div class="flex-1 max-w-2xl mx-2">
                    <livewire:search-bar />
                </div>

                <!-- Right Actions: Language, Account, Wishlist, Cart -->
                <div class="flex items-center gap-2 sm:gap-4 shrink-0">
                    <!-- Mobile Language Switch -->
                    <a href="{{ route('locale.set', ($currentLocale ?? 'ar') === 'ar' ? 'en' : 'ar') }}" class="sm:hidden text-xs font-bold px-2 py-1 rounded bg-stone-100 text-stone-700">
                        {{ ($currentLocale ?? 'ar') === 'ar' ? 'EN' : 'عربي' }}
                    </a>

                    <!-- Account / Auth -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-1.5 p-2 rounded-lg text-stone-700 hover:bg-stone-100 focus:outline-none transition-colors">
                            <svg class="w-5 h-5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="hidden md:inline text-xs font-semibold">
                                @auth
                                    {{ Str::limit(auth()->user()->name, 12) }}
                                @else
                                    {{ __('messages.sign_in') }}
                                @endauth
                            </span>
                        </button>
                        <div x-show="open" x-cloak class="absolute {{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'left-0' : 'right-0' }} mt-2 w-56 rounded-xl bg-white shadow-lg border border-stone-100 py-2 z-50 text-xs">
                            @auth
                                <div class="px-4 py-2 border-b border-stone-100">
                                    <p class="font-bold text-stone-900">{{ auth()->user()->name }}</p>
                                    <p class="text-stone-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 text-stone-700 hover:bg-stone-50 font-medium">{{ __('messages.my_account') }}</a>
                                <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-stone-700 hover:bg-stone-50 font-medium">{{ __('messages.my_orders') }}</a>
                                @if(auth()->user()->isSeller())
                                    <a href="{{ route('seller.dashboard') }}" class="block px-4 py-2 text-amber-600 hover:bg-amber-50 font-bold border-t border-stone-100">{{ __('messages.seller_dashboard') }}</a>
                                @else
                                    <a href="{{ route('seller.apply') }}" class="block px-4 py-2 text-amber-600 hover:bg-amber-50 font-bold border-t border-stone-100">ابدأ البيع كتاجر</a>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-red-600 hover:bg-red-50 font-bold border-t border-stone-100">{{ __('messages.admin_panel') }}</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100">
                                    @csrf
                                    <button type="submit" class="w-full text-start px-4 py-2 text-stone-600 hover:bg-stone-50 font-medium">{{ __('messages.logout') }}</button>
                                </form>
                            @else
                                <div class="px-4 py-3 border-b border-stone-100 text-center">
                                    <p class="font-bold text-stone-900 mb-1">أهلاً بك في جوردن مارت</p>
                                    <p class="text-stone-500 mb-3">سجّل الدخول لتجربة تسوق مخصصة</p>
                                    <a href="{{ route('login') }}" class="inline-block w-full py-2 px-3 bg-amber-500 hover:bg-amber-600 text-stone-900 font-bold rounded-lg transition-colors text-center shadow-xs">{{ __('messages.sign_in') }}</a>
                                    <a href="{{ route('register') }}" class="inline-block w-full mt-2 py-1.5 px-3 border border-stone-200 hover:bg-stone-50 text-stone-700 font-medium rounded-lg transition-colors text-center">{{ __('messages.register') }}</a>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Cart Counter Component -->
                    <livewire:cart-counter />
                </div>
            </div>
        </div>

        <!-- Secondary Navigation & Mega Menu Bar -->
        <nav class="bg-stone-900 text-stone-100 text-xs hidden lg:block border-t border-stone-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center space-x-1 rtl:space-x-reverse" x-data="{ megaMenuOpen: false }">
                    
                    <!-- Mega Menu Trigger -->
                    <div class="relative" @click.outside="megaMenuOpen = false">
                        <button type="button" @click="megaMenuOpen = !megaMenuOpen" class="flex items-center gap-2 py-3 px-4 font-bold bg-amber-500 text-stone-950 hover:bg-amber-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <span>{{ __('messages.all_categories') }}</span>
                            <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': megaMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Mega Menu Dropdown -->
                        <div x-show="megaMenuOpen" x-cloak class="absolute {{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'right-0' : 'left-0' }} top-full w-[780px] bg-white text-stone-900 shadow-2xl rounded-b-2xl border border-stone-200 p-6 z-50 grid grid-cols-3 gap-6">
                            @php
                                $menuCategories = \App\Models\Category::whereNull('parent_id')->where('is_active', true)->with('children')->get();
                            @endphp
                            @foreach($menuCategories as $cat)
                                <div class="space-y-2">
                                    <a href="{{ route('storefront.category', $cat->slug) }}" class="font-bold text-sm text-stone-950 hover:text-amber-600 flex items-center justify-between pb-1.5 border-b border-stone-100">
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-xs text-stone-600 font-normal">→</span>
                                    </a>
                                    @if($cat->children->isNotEmpty())
                                        <ul class="space-y-1.5 text-xs text-stone-600">
                                            @foreach($cat->children->take(4) as $sub)
                                                <li>
                                                    <a href="{{ route('storefront.category', $sub->slug) }}" class="hover:text-amber-600 hover:underline transition-colors">
                                                        {{ $sub->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Direct Fast Links -->
                    <a href="{{ route('storefront.home') }}" class="py-3 px-3.5 font-medium hover:text-amber-400 transition-colors">الرئيسية</a>
                    <a href="{{ route('storefront.catalog') }}" class="py-3 px-3.5 font-medium hover:text-amber-400 transition-colors">كافة المنتجات</a>
                    <a href="{{ route('storefront.category', 'electronics') }}" class="py-3 px-3.5 font-medium hover:text-amber-400 transition-colors">الإلكترونيات والهواتف</a>
                    <a href="{{ route('storefront.category', 'fashion') }}" class="py-3 px-3.5 font-medium hover:text-amber-400 transition-colors">الأزياء والملابس</a>
                    <a href="{{ route('storefront.category', 'beauty-perfumes') }}" class="py-3 px-3.5 font-medium hover:text-amber-400 transition-colors">العطور والجمال</a>
                    <a href="{{ route('storefront.catalog') }}?deals=1" class="py-3 px-3.5 font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.533 1.035-.688 1.572a9.98 9.98 0 00-.317 2.052c-.05.74-.085 1.554-.085 2.328 0 .805.045 1.58.106 2.302.048.56.12 1.116.223 1.66.195 1.037.49 2.015.897 2.915.204.45.452.87.737 1.25.284.382.617.72.99 1.004.372.285.79.5 1.233.633.444.133.91.18 1.378.14.935-.08 1.808-.475 2.483-1.123.676-.648 1.115-1.503 1.248-2.434.133-.93-.05-1.875-.52-2.686a6.994 6.994 0 00-1.63-1.87 9.97 9.97 0 00-2.316-1.438 12.02 12.02 0 00-1.282-.544c-.2-.07-.403-.135-.609-.193z" clip-rule="evenodd"/>
                        </svg>
                        <span>عروض اليوم</span>
                    </a>
                </div>
                <div class="flex items-center space-x-3 rtl:space-x-reverse text-stone-300">
                    <span class="text-[11px] text-amber-300 font-semibold bg-stone-800 px-2.5 py-1 rounded-full border border-stone-700">🇯🇴 شحن لكافة المحافظات</span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mobile Drawer Navigation -->
    <div x-show="mobileMenuOpen" x-cloak class="relative z-50 lg:hidden">
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-xs transition-opacity" @click="mobileMenuOpen = false"></div>
        <div class="fixed inset-y-0 {{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'right-0' : 'left-0' }} max-w-xs w-full bg-white shadow-xl p-6 overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-stone-100">
                <span class="font-bold text-stone-900 text-lg">قائمة التصفح</span>
                <button type="button" @click="mobileMenuOpen = false" class="p-1 rounded text-stone-500 hover:text-stone-800">✕</button>
            </div>
            <div class="py-4 space-y-3">
                <a href="{{ route('storefront.home') }}" class="block font-bold text-stone-900 py-1.5">الرئيسية</a>
                <a href="{{ route('storefront.catalog') }}" class="block font-medium text-stone-700 py-1.5">جميع المنتجات</a>
                <a href="{{ route('storefront.category', 'electronics') }}" class="block font-medium text-stone-700 py-1.5">الإلكترونيات والهواتف</a>
                <a href="{{ route('storefront.category', 'fashion') }}" class="block font-medium text-stone-700 py-1.5">الأزياء والملابس</a>
                <a href="{{ route('storefront.category', 'beauty-perfumes') }}" class="block font-medium text-stone-700 py-1.5">العطور والجمال</a>
            </div>
        </div>
    </div>

    <!-- Location Modal (Jordan Governorates & Cities) -->
    <div x-show="locationModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-100" @click.outside="locationModalOpen = false">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="font-bold text-base text-stone-900">اختر مدينة التوصيل في الأردن</h3>
                <button type="button" @click="locationModalOpen = false" class="text-stone-400 hover:text-stone-600">✕</button>
            </div>
            <p class="text-xs text-stone-500 mt-2">اختر موقعك لعرض رسوم وأوقات التوصيل الدقيقة إلى باب منزلك:</p>
            <div class="grid grid-cols-2 gap-2 mt-4 text-xs">
                @php
                    $jordanCities = ['عمّان', 'الزرقاء', 'إربد', 'العقبة', 'السلط / البلقاء', 'مأدبا', 'جرش', 'عجلون', 'الكرك', 'معان', 'المفرق', 'الطفيلة'];
                @endphp
                @foreach($jordanCities as $city)
                    <button type="button" @click="selectedCity = '{{ $city }}، الأردن'; locationModalOpen = false" class="p-2.5 rounded-xl border border-stone-200 text-stone-700 hover:border-amber-500 hover:bg-amber-50 hover:text-amber-800 text-start font-medium transition-all">
                        📍 {{ $city }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Toast Notification Banner -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3500)"
         x-show="show"
         x-transition
         x-cloak
         class="fixed bottom-6 {{ ($isRtl ?? (app()->getLocale() === 'ar')) ? 'left-6' : 'right-6' }} z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-2xl text-white text-xs font-bold"
         :class="type === 'success' ? 'bg-emerald-600' : 'bg-red-600'">
        <span x-text="message"></span>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Comprehensive E-Commerce Footer -->
    <footer class="bg-stone-900 text-stone-300 text-xs border-t border-stone-800 mt-16">
        <!-- Trust Banner -->
        <div class="border-b border-stone-800 py-8 bg-stone-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-6 text-center md:text-start">
                <div class="flex items-center gap-4 justify-center md:justify-start">
                    <div class="w-12 h-12 rounded-xl bg-stone-800 text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">توصيل سريع وموثوق</h4>
                        <p class="text-stone-400 text-[11px]">شحن لجميع محافظات المملكة الأردنية الهاشمية</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 justify-center md:justify-start">
                    <div class="w-12 h-12 rounded-xl bg-stone-800 text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">الدفع عند الاستلام (COD)</h4>
                        <p class="text-stone-400 text-[11px]">ادفع نقداً عند استلام طلبك بأمان تام</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 justify-center md:justify-start">
                    <div class="w-12 h-12 rounded-xl bg-stone-800 text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">كفالة أردنية معتمدة</h4>
                        <p class="text-stone-400 text-[11px]">منتجات أصلية 100% مع ضمان الوكلاء المحليين</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 justify-center md:justify-start">
                    <div class="w-12 h-12 rounded-xl bg-stone-800 text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">خدمة عملاء على مدار الساعة</h4>
                        <p class="text-stone-400 text-[11px]">فريق دعم محلي جاهز لمساعدتك في أي وقت</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="font-black text-xl text-white">Jordan<span class="text-red-500">Mart</span></span>
                </div>
                <p class="text-stone-400 text-xs leading-relaxed">
                    منصة التجارة الإلكترونية الأردنية الرائدة التي تجمع نخبة التجار والمتاجر الموثقة في المملكة تحت سقف رقمي واحد متطور.
                </p>
                <div class="pt-2 text-stone-400 text-[11px]">
                    📍 عمّان، المملكة الأردنية الهاشمية
                </div>
            </div>
            <div>
                <h4 class="font-bold text-white mb-3 text-sm">روابط التسوق</h4>
                <ul class="space-y-2 text-stone-400">
                    <li><a href="{{ route('storefront.catalog') }}" class="hover:text-amber-400">تصفح كافة المنتجات</a></li>
                    <li><a href="{{ route('storefront.category', 'electronics') }}" class="hover:text-amber-400">الإلكترونيات والهواتف</a></li>
                    <li><a href="{{ route('storefront.category', 'fashion') }}" class="hover:text-amber-400">الأزياء والملابس</a></li>
                    <li><a href="{{ route('storefront.category', 'beauty-perfumes') }}" class="hover:text-amber-400">العطور والجمال</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-3 text-sm">خدمة العملاء</h4>
                <ul class="space-y-2 text-stone-400">
                    <li><a href="#" class="hover:text-amber-400">سياسة الإرجاع والاسترداد</a></li>
                    <li><a href="#" class="hover:text-amber-400">مناطق ورسوم التوصيل في الأردن</a></li>
                    <li><a href="#" class="hover:text-amber-400">طرق الدفع ومشتريات الأقساط</a></li>
                    <li><a href="#" class="hover:text-amber-400">تتبع الطلبات والشحنات</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-3 text-sm">التجار والبائعون</h4>
                <ul class="space-y-2 text-stone-400">
                    <li><a href="{{ route('seller.apply') }}" class="hover:text-amber-400">ابدأ البيع على جوردن مارت</a></li>
                    <li><a href="{{ route('seller.apply') }}" class="hover:text-amber-400">شروط الانضمام والتوثيق التجاري</a></li>
                    <li><a href="{{ route('seller.dashboard') }}" class="hover:text-amber-400">لوحة تحكم البائعين</a></li>
                    <li><a href="#" class="hover:text-amber-400">حماية حقوق المستهلك والتاجر</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-stone-800 py-6 text-center text-stone-500 text-[11px]">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>© {{ date('Y') }} جوردن مارت (JordanMart). جميع الحقوق محفوظة — المملكة الأردنية الهاشمية.</p>
                <div class="flex items-center gap-4 text-stone-400">
                    <span>دعم CliQ الفوري</span>
                    <span>•</span>
                    <span>بطاقات الائتمان</span>
                    <span>•</span>
                    <span>الدفع عند الاستلام</span>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
