<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'بوابة التجار — JordanMart')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: {{ app()->getLocale() === 'ar' ? "'Cairo', sans-serif" : "'Instrument Sans', sans-serif" }};
        }
    </style>
</head>
<body class="min-h-full flex bg-stone-100 text-stone-900 antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 bg-stone-900/60 backdrop-blur-xs lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '{{ app()->getLocale() === 'ar' ? 'translate-x-full' : '-translate-x-full' }}'"
           class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-50 w-64 bg-stone-900 text-stone-200 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between shrink-0">
        
        <div>
            <!-- Store Branding -->
            <div class="h-16 flex items-center justify-between px-6 bg-stone-950 border-b border-stone-800">
                <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 text-stone-950 font-black flex items-center justify-center text-sm">
                        JM
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-white">JordanMart</span>
                        <span class="text-[10px] text-amber-400 font-semibold">بوابة التجار المستقلين</span>
                    </div>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden text-stone-400 hover:text-white">✕</button>
            </div>

            <!-- Seller Store Info -->
            <div class="px-6 py-4 border-b border-stone-800/80 bg-stone-900/50">
                <p class="text-xs text-stone-400">المتجر الحالي:</p>
                <p class="text-sm font-bold text-white truncate">{{ auth()->user()->seller?->store_name ?? 'متجر البائع' }}</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        {{ auth()->user()->seller?->status === 'approved' ? 'موثق ومعتمد' : auth()->user()->seller?->status }}
                    </span>
                    <span class="text-[11px] text-stone-400">عمولة: {{ auth()->user()->seller?->commission_rate }}%</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1 text-xs font-semibold">
                <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.dashboard') ? 'bg-amber-500 text-stone-950 font-bold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>لوحة التحكم الرئيسية</span>
                </a>

                <a href="{{ route('seller.offers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.offers.*') ? 'bg-amber-500 text-stone-950 font-bold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>عروض المنتجات والأسعار</span>
                </a>

                <a href="{{ route('seller.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.orders.*') ? 'bg-amber-500 text-stone-950 font-bold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>الطلبات والشحن والتجهيز</span>
                </a>

                <a href="{{ route('seller.inventory.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.inventory.*') ? 'bg-amber-500 text-stone-950 font-bold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>إدارة المخزون والتوريد</span>
                </a>

                <a href="{{ route('seller.payouts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.payouts.*') ? 'bg-amber-500 text-stone-950 font-bold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>المستحقات وسحب الأرباح</span>
                </a>

                <a href="{{ route('seller.profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('seller.profile.*') ? 'bg-amber-500 text-stone-950 font-bold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>إعدادات المتجر والحساب البنكي</span>
                </a>
            </nav>
        </div>

        <!-- Footer / Return to Storefront -->
        <div class="p-4 border-t border-stone-800 text-xs">
            <a href="{{ route('storefront.home') }}" class="flex items-center gap-2 text-stone-400 hover:text-amber-400 py-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>العودة للسوق</span>
            </a>
            <div class="mt-2 pt-2 border-t border-stone-800/60 flex items-center justify-between text-[11px] text-stone-500">
                <span>{{ auth()->user()->name }}</span>
                <span class="text-amber-500 font-bold">بائع</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top bar -->
        <header class="h-16 bg-white border-b border-stone-200 px-4 sm:px-6 flex items-center justify-between gap-4 sticky top-0 z-30 shadow-xs">
            <div class="flex items-center gap-3">
                <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-stone-600 hover:bg-stone-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-base sm:text-lg font-black text-stone-900 truncate">@yield('page_title', 'لوحة التحكم')</h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-end">
                    <p class="text-xs font-bold text-stone-900 leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-stone-500 mt-0.5">{{ auth()->user()->email }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-500 text-stone-950 font-black flex items-center justify-center text-sm shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-4">
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        <span>{{ session('error') ?? __('Please correct the errors below:') }}</span>
                    </div>
                    @if($errors->any())
                        <ul class="list-disc list-inside ps-6 space-y-0.5 font-medium text-red-700">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @endif

        <!-- Body content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
            @yield('content')
        </main>
    </div>

</body>
</html>
