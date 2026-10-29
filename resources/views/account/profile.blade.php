@extends('account.layout')

@section('title', __('messages.edit_profile') . ' — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-xs max-w-xl">
    <div class="border-b border-stone-100 pb-4 mb-6">
        <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
            <span>⚙️</span>
            <span>{{ __('messages.edit_profile') }}</span>
        </h2>
        <p class="text-xs text-stone-500 mt-0.5">تحديث معلومات حسابك الشخصي وكلمة المرور.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-5 text-xs">
        @csrf
        @method('PUT')

        <!-- Full Name -->
        <div>
            <label class="block font-bold text-stone-700 mb-1">{{ __('messages.full_name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-stone-300 p-2.5 focus:border-stone-900">
        </div>

        <!-- Email (Disabled) -->
        <div>
            <label class="block font-bold text-stone-700 mb-1">البريد الإلكتروني</label>
            <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-xl border border-stone-200 bg-stone-50 p-2.5 text-stone-500 cursor-not-allowed">
            <span class="text-[10px] text-stone-400 mt-1 block">البريد الإلكتروني مرتبط بهوية الحساب ولا يمكن تغييره مباشرة.</span>
        </div>

        <!-- Phone Number -->
        <div>
            <label class="block font-bold text-stone-700 mb-1">{{ __('messages.phone_number') }} (أردني) *</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full rounded-xl border border-stone-300 p-2.5 focus:border-stone-900">
            <span class="text-[10px] text-stone-400 mt-1 block">{{ __('messages.jordan_phone_hint') }}</span>
        </div>

        <!-- Change Password Section -->
        <div class="pt-5 border-t border-stone-100 space-y-4">
            <h3 class="font-black text-stone-900 text-xs">{{ __('messages.change_password') }} (اتركه فارغاً إذا كنت لا ترغب بتغييره)</h3>

            <div>
                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.current_password') }}</label>
                <input type="password" name="current_password" class="w-full rounded-xl border border-stone-300 p-2.5 focus:border-stone-900">
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.new_password') }}</label>
                <input type="password" name="new_password" class="w-full rounded-xl border border-stone-300 p-2.5 focus:border-stone-900">
            </div>

            <div>
                <label class="block font-bold text-stone-700 mb-1">{{ __('messages.password_confirmation') }}</label>
                <input type="password" name="new_password_confirmation" class="w-full rounded-xl border border-stone-300 p-2.5 focus:border-stone-900">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-black text-xs shadow-sm transition-all">
                حفظ التعديلات
            </button>
        </div>
    </form>
</div>
@endsection
