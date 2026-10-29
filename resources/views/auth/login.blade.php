@extends('layouts.storefront')

@section('title', __('messages.login_title') . ' — JordanMart')

@section('content')
<div class="py-12 bg-stone-50 min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-stone-200 p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-600 via-stone-900 to-emerald-700 items-center justify-center text-white shadow-sm ring-4 ring-stone-100 mb-3">
                <span class="font-black text-2xl tracking-tighter">JM</span>
            </div>
            <h1 class="text-xl font-black text-stone-900 tracking-tight">{{ __('messages.login_title') }}</h1>
            <p class="text-xs text-stone-500 mt-1">سوق الأردن الإلكتروني الموثوق متعدد البائعين</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Login Identifier (Email or Jordan Phone) -->
            <div>
                <label for="login" class="block text-xs font-bold text-stone-700 mb-1">
                    {{ __('messages.email_or_phone') }}
                </label>
                <div class="relative">
                    <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                        placeholder="0791234567 / name@example.com"
                        class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors @error('login') border-red-500 @enderror">
                </div>
                <p class="text-[10px] text-stone-400 mt-1">{{ __('messages.jordan_phone_hint') }}</p>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold text-stone-700">
                        {{ __('messages.password') }}
                    </label>
                </div>
                <input id="password" name="password" type="password" required
                    class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors @error('password') border-red-500 @enderror">
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-stone-300 text-amber-500 focus:ring-amber-400">
                    <span class="text-stone-600">{{ __('messages.remember_me') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-black text-sm shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 mt-2">
                <span>{{ __('messages.sign_in') }}</span>
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <!-- Register Link -->
        <div class="mt-6 pt-6 border-t border-stone-100 text-center text-xs text-stone-600">
            <span>{{ __('messages.dont_have_account') }}</span>
            <a href="{{ route('register') }}" class="font-bold text-red-600 hover:text-red-700 ms-1 underline underline-offset-2">
                {{ __('messages.register_now') }}
            </a>
        </div>
    </div>
</div>
@endsection
