@extends('account.layout')

@section('title', __('messages.saved_addresses') . ' — JordanMart')

@section('account_content')
<div class="space-y-6" x-data="{ showAddModal: false }">
    
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
                <span>📍</span>
                <span>{{ __('messages.saved_addresses') }}</span>
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">أدر عناوين التوصيل الخاصة بك لاستخدامها بسهولة وسرعة عند الدفع.</p>
        </div>

        <button type="button" @click="showAddModal = true" class="px-4 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs transition-colors">
            + {{ __('messages.add_new_address') }}
        </button>
    </div>

    <!-- Addresses Grid -->
    @if($addresses->isEmpty())
        <div class="bg-white rounded-2xl border border-stone-200 p-12 text-center text-xs text-stone-500">
            <span class="text-3xl block mb-2">📍</span>
            <p class="font-bold text-stone-800 mb-1">لا توجد عناوين محفوظة بعد</p>
            <p class="mb-4">أضف عنوان توصيل في الأردن لتسريع عملية الشراء.</p>
            <button type="button" @click="showAddModal = true" class="px-4 py-2 rounded-xl bg-amber-500 text-stone-950 font-bold">
                + {{ __('messages.add_new_address') }}
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($addresses as $address)
                <div class="bg-white rounded-2xl border border-stone-200 p-5 shadow-xs relative flex flex-col justify-between text-xs space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-bold text-stone-900 text-sm">{{ $address->contact_name }}</span>
                            @if($address->is_default)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    افتراضي
                                </span>
                            @endif
                        </div>
                        <p class="text-stone-500 font-mono text-[11px] mb-2">📞 {{ $address->phone }}</p>
                        <p class="text-stone-700 leading-relaxed">{{ $address->formatted_address }}</p>
                        @if($address->delivery_instructions)
                            <p class="text-[11px] text-stone-400 mt-2 bg-stone-50 p-2 rounded-lg italic">
                                ملاحظات: {{ $address->delivery_instructions }}
                            </p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-stone-100 flex items-center justify-between">
                        @if(! $address->is_default)
                            <form method="POST" action="{{ route('account.addresses.default', $address->id) }}">
                                @csrf
                                <button type="submit" class="text-stone-500 hover:text-stone-900 font-semibold text-[11px]">
                                    {{ __('messages.set_as_default') }}
                                </button>
                            </form>
                        @else
                            <span class="text-[11px] text-stone-400">العنوان الأساسي</span>
                        @endif

                        <form method="POST" action="{{ route('account.addresses.delete', $address->id) }}" onsubmit="return confirm('هل تريد حذف هذا العنوان؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 font-bold text-[11px]">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Add Address Modal -->
    <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 text-xs text-start shadow-xl border border-stone-200" @click.outside="showAddModal = false">
            <h3 class="text-sm font-black text-stone-900 mb-4 pb-2 border-b border-stone-100">{{ __('messages.add_new_address') }}</h3>

            <form method="POST" action="{{ route('account.addresses.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.full_name') }} *</label>
                        <input type="text" name="contact_name" value="{{ auth()->user()->name }}" required class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.phone_number') }} *</label>
                        <input type="text" name="phone" value="{{ auth()->user()->phone }}" placeholder="0791234567" required class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.governorate') }} *</label>
                        <select name="governorate" required class="w-full rounded-xl border border-stone-300 p-2.5">
                            @foreach($governorates as $key => $name)
                                <option value="{{ $key }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.city') }} / المنطقة *</label>
                        <input type="text" name="city" required placeholder="مثال: الجبيهة، صويلح..." class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.street_address') }} *</label>
                        <input type="text" name="street_address" required placeholder="اسم الشارع" class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.building_number') }}</label>
                        <input type="text" name="building_number" placeholder="رقم البناية" class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.apartment') }} / الطابق</label>
                        <input type="text" name="apartment" placeholder="شقة 3، طابق 2" class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-bold text-stone-700 mb-1">{{ __('messages.delivery_instructions') }}</label>
                        <input type="text" name="delivery_instructions" placeholder="ملاحظات للمندوب" class="w-full rounded-xl border border-stone-300 p-2.5">
                    </div>

                    <div class="sm:col-span-2 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_default" value="1" class="rounded border-stone-300 text-amber-500">
                            <span class="text-stone-700 font-semibold">{{ __('messages.set_as_default') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-stone-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-700 font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold">حفظ العنوان</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
