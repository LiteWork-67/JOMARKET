@extends('layouts.storefront')

@section('content')
<div class="py-10 bg-stone-50 min-h-[80vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Greeting Header -->
        <div class="mb-8">
            <nav class="flex items-center gap-2 text-xs text-stone-500 mb-2">
                <a href="{{ route('storefront.home') }}" class="hover:text-stone-900 transition-colors">{{ __('messages.app_name') }}</a>
                <span>/</span>
                <span class="text-stone-900 font-semibold">{{ __('messages.my_account') }}</span>
            </nav>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-stone-900 text-white flex items-center justify-center text-lg font-black shadow-xs">
                        {{ Str::substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-stone-900">أهلاً بك، {{ auth()->user()->name }}</h1>
                        <p class="text-xs text-stone-500">{{ auth()->user()->email }} • {{ auth()->user()->phone ?? 'لم يُحدد هاتف' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center gap-2 shadow-xs">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- Account Navigation Sidebar (1/4) -->
            <div class="space-y-2">
                <div class="bg-white rounded-2xl border border-stone-200 p-3 shadow-xs space-y-1 text-xs">
                    <a href="{{ route('account.dashboard') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.dashboard') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>📊</span>
                        <span>{{ __('messages.account_overview') }}</span>
                    </a>

                    <a href="{{ route('account.orders') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.orders*') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>📦</span>
                        <span>{{ __('messages.my_orders') }}</span>
                    </a>

                    <a href="{{ route('account.addresses') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.addresses*') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>📍</span>
                        <span>{{ __('messages.saved_addresses') }}</span>
                    </a>

                    <a href="{{ route('account.wishlist') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.wishlist*') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>❤️</span>
                        <span>{{ __('messages.wishlist') }}</span>
                    </a>

                    <a href="{{ route('account.returns.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.returns*') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>🔄</span>
                        <span>طلبات الإرجاع</span>
                    </a>

                    <a href="{{ route('account.tickets.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.tickets*') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>💬</span>
                        <span>الدعم والمساعدة</span>
                    </a>

                    <a href="{{ route('account.profile') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold transition-all {{ request()->routeIs('account.profile*') ? 'bg-amber-500/10 text-amber-900 border border-amber-200' : 'text-stone-700 hover:bg-stone-50' }}">
                        <span>⚙️</span>
                        <span>{{ __('messages.edit_profile') }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-stone-100">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-bold text-red-600 hover:bg-red-50 transition-colors text-start">
                            <span>🚪</span>
                            <span>{{ __('messages.logout') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Content Area (3/4) -->
            <div class="lg:col-span-3">
                @yield('account_content')
            </div>

        </div>

    </div>
</div>
@endsection
