@extends('layouts.seller')

@section('title', 'إعدادات المتجر والحساب — JordanMart')
@section('page_title', 'إعدادات المتجر والحساب')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black text-stone-900">الملف التجاري لمتجرك</h2>
            <p class="text-xs text-stone-500 mt-0.5">تحديث بيانات الاتصال، معلومات المتجر العامة، وتفاصيل الحساب البنكي.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('seller.profile.update') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-6">
        @csrf
        @method('PUT')

        <!-- System Readonly Data -->
        <div class="p-4 rounded-xl bg-stone-50 border border-stone-200 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <span class="text-stone-500 font-semibold">حالة المتجر:</span>
                <span class="block font-bold text-emerald-700 mt-0.5">{{ $seller->status === 'approved' ? 'موثق ومعتمد رسمي' : $seller->status }}</span>
            </div>
            <div>
                <span class="text-stone-500 font-semibold">نسبة العمولة المطبقة:</span>
                <span class="block font-bold text-stone-900 mt-0.5">{{ $seller->commission_rate }}%</span>
            </div>
            <div>
                <span class="text-stone-500 font-semibold">رقم السجل التجاري:</span>
                <span class="block font-bold font-mono text-stone-900 mt-0.5">{{ $seller->commercial_registration_no ?: 'غير مسجل' }}</span>
            </div>
        </div>

        <!-- Store Names -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">اسم المتجر (بالعربية) <span class="text-red-600">*</span></label>
                <input type="text" name="store_name_ar" value="{{ old('store_name_ar', $seller->store_name_ar) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">اسم المتجر (بالإنجليزية) <span class="text-red-600">*</span></label>
                <input type="text" name="store_name_en" value="{{ old('store_name_en', $seller->store_name_en) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <!-- Descriptions -->
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">وصف المتجر (بالعربية)</label>
                <textarea name="description_ar" rows="3" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">{{ old('description_ar', $seller->description_ar) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">وصف المتجر (بالإنجليزية)</label>
                <textarea name="description_en" rows="3" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">{{ old('description_en', $seller->description_en) }}</textarea>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">البريد الإلكتروني التجاري <span class="text-red-600">*</span></label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $seller->contact_email) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رقم الهاتف التجاري <span class="text-red-600">*</span></label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $seller->contact_phone) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <!-- Location -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">المحافظة</label>
                <input type="text" name="governorate" value="{{ old('governorate', $seller->governorate) }}" placeholder="مثال: عمّان" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">المدينة / المنطقة</label>
                <input type="text" name="city" value="{{ old('city', $seller->city) }}" placeholder="مثال: شارع الجاردنز" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">تفاصيل العنوان</label>
                <input type="text" name="address_details" value="{{ old('address_details', $seller->address_details) }}" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <!-- Bank Details Section -->
        <div class="pt-4 border-t border-stone-100">
            <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider mb-4">بيانات الحساب البنكي الأردني</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">اسم البنك</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $seller->bank_name) }}" placeholder="مثال: البنك العربي" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">رقم الآيبان الدولي (IBAN)</label>
                    <input type="text" name="iban" value="{{ old('iban', $seller->iban) }}" placeholder="JO..." dir="ltr" class="w-full text-xs font-mono rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 mb-1">اسم صاحب الحساب</label>
                    <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $seller->account_holder_name) }}" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end">
            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors">
                حفظ التعديلات
            </button>
        </div>
    </form>

</div>
@endsection
