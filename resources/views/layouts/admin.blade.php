<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة إدارة المنصة — JordanMart')</title>

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

    <!-- Admin Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '{{ app()->getLocale() === 'ar' ? 'translate-x-full' : '-translate-x-full' }}'"
           class="fixed inset-y-0 {{ app()->getLocale() === 'ar' ? 'right-0' : 'left-0' }} z-50 w-64 bg-stone-950 text-stone-200 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between shrink-0">
        
        <div>
            <!-- Header Brand -->
            <div class="h-16 flex items-center justify-between px-6 bg-black border-b border-stone-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-600 text-white font-black flex items-center justify-center text-sm shadow-xs">
                        JM
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-white">Jordan<span class="text-red-500">Mart</span></span>
                        <span class="text-[10px] text-stone-400 font-semibold">إدارة المنصة المركزية</span>
                    </div>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden text-stone-400 hover:text-white">✕</button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1 text-xs font-semibold overflow-y-auto max-h-[calc(100vh-10rem)]">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>لوحة المؤشرات والتحليلات</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-stone-500 uppercase tracking-wider">
                    إدارة التجار والكتالوج
                </div>

                <a href="{{ route('admin.sellers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.sellers.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>التجار والطلبات المقدمة</span>
                </a>

                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>مراجعة واعتماد المنتجات</span>
                </a>

                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span>كافة طلبات السوق</span>
                </a>

                <a href="{{ route('admin.disputes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.disputes.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>النزاعات وطلبات الإرجاع</span>
                </a>

                <a href="{{ route('admin.support.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.support.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span>تذاكر الدعم والعملاء</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-stone-500 uppercase tracking-wider">
                    المالية والمستحقات
                </div>

                <a href="{{ route('admin.finance.commissions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.finance.commissions') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    <span>تقرير العمولات والإيرادات</span>
                </a>

                <a href="{{ route('admin.finance.payouts') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.finance.payouts') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>طلبات تحويل الأرباح</span>
                </a>

                <a href="{{ route('admin.finance.cod.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.finance.cod.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>تسوية الدفع عند الاستلام</span>
                </a>

                <a href="{{ route('admin.finance.ledger') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.finance.ledger') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>سجل الحسابات المالي</span>
                </a>

                <div class="pt-3 pb-1 px-3 text-[10px] font-bold text-stone-500 uppercase tracking-wider">
                    إدارة النظام والتصنيفات
                </div>

                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>التصنيفات والأقسام</span>
                </a>

                <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span>العلامات التجارية</span>
                </a>

                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.audit-logs.*') ? 'bg-red-600 text-white font-bold' : 'text-stone-300 hover:bg-stone-900 hover:text-white' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>سجل الرقابة والتدقيق</span>
                </a>
            </nav>
        </div>

        <!-- Footer -->
        <div class="p-4 border-t border-stone-800 text-xs">
            <a href="{{ route('storefront.home') }}" class="flex items-center gap-2 text-stone-400 hover:text-white py-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>معاينة واجهة المتجر</span>
            </a>
            <div class="mt-2 pt-2 border-t border-stone-800/60 flex items-center justify-between text-[11px] text-stone-500">
                <span>{{ auth()->user()->name }}</span>
                <span class="text-red-400 font-bold">مسؤول النظام</span>
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
                <h1 class="text-base sm:text-lg font-black text-stone-900 truncate">@yield('page_title', 'لوحة الإدارة')</h1>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-end">
                    <p class="text-xs font-bold text-stone-900 leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-stone-500 mt-0.5">{{ auth()->user()->email }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-red-600 text-white font-black flex items-center justify-center text-sm shadow-xs">
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
