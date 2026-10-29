@extends('layouts.storefront')

@section('title', __('messages.register_title') . ' — JordanMart')

@section('content')
<div class="py-12 bg-stone-50 min-h-[70vh] flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-stone-200 p-6 sm:p-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-600 via-stone-900 to-emerald-700 items-center justify-center text-white shadow-sm ring-4 ring-stone-100 mb-3">
                <span class="font-black text-2xl tracking-tighter">JM</span>
            </div>
            <h1 class="text-xl font-black text-stone-900 tracking-tight">{{ __('messages.register_title') }}</h1>
            <p class="text-xs text-stone-500 mt-1">انضم إلى آلاف المتسوقين في المملكة الأردنية الهاشمية</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-bold text-stone-700 mb-1">
                    {{ __('messages.full_name') }}
                </label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    placeholder="مثال: أحمد عبد الله"
                    class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors @error('name') border-red-500 @enderror">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-stone-700 mb-1">
                    البريد الإلكتروني
                </label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    placeholder="ahmad@example.com"
                    class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors @error('email') border-red-500 @enderror">
            </div>

            <!-- Jordanian Phone Number -->
            <div>
                <label for="phone" class="block text-xs font-bold text-stone-700 mb-1">
                    {{ __('messages.phone_number') }}
                </label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required
                    placeholder="0791234567"
                    class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors @error('phone') border-red-500 @enderror">
                <p class="text-[10px] text-stone-400 mt-1">{{ __('messages.jordan_phone_hint') }}</p>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-xs font-bold text-stone-700 mb-1">
                    {{ __('messages.password') }}
                </label>
                <input id="password" name="password" type="password" required
                    class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors @error('password') border-red-500 @enderror">
            </div>

            <!-- Password Confirmation -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-stone-700 mb-1">
                    {{ __('messages.password_confirmation') }}
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="w-full text-xs rounded-xl border border-stone-300 px-3.5 py-2.5 focus:border-stone-900 focus:ring-1 focus:ring-stone-900 transition-colors">
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-black text-sm shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 mt-2">
                <span>{{ __('messages.register') }}</span>
                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 pt-6 border-t border-stone-100 text-center text-xs text-stone-600">
            <span>{{ __('messages.have_account') }}</span>
            <a href="{{ route('login') }}" class="font-bold text-red-600 hover:text-red-700 ms-1 underline underline-offset-2">
                {{ __('messages.sign_in') }}
            </a>
        </div>
    </div>
</div>
@endsection
