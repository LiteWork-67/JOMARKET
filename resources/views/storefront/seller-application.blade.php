@extends('layouts.storefront')

@section('title', 'انضم كبائع وشريك تجاري — JordanMart')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="text-center max-w-xl mx-auto mb-8">
        <div class="w-12 h-12 rounded-2xl bg-amber-500 text-stone-950 font-black flex items-center justify-center text-lg mx-auto mb-3 shadow-xs">
            JM
        </div>
        <h1 class="text-2xl font-black text-stone-900">انضم كتاجر معتمد على جوردن مارت</h1>
        <p class="text-xs text-stone-600 mt-2">وسع نطاق تجارتك واعرض منتجاتك لآلاف المشترين عبر كافة محافظات المملكة الأردنية الهاشمية من خلال شبكة لوجستية وبنكية متكاملة.</p>
    </div>

    @if(session('error'))
        <div class="p-4 mb-6 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('seller.apply.store') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        @csrf

        <div class="border-b border-stone-100 pb-3">
            <h3 class="text-sm font-black text-stone-900">البيانات الأساسية للمتجر</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">اسم المتجر بالعربية <span class="text-red-600">*</span></label>
                <input type="text" name="store_name_ar" value="{{ old('store_name_ar') }}" required placeholder="مثال: متجر عمّان للتكنولوجيا" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">اسم المتجر بالإنجليزية <span class="text-red-600">*</span></label>
                <input type="text" name="store_name_en" value="{{ old('store_name_en') }}" required placeholder="Amman Tech Store" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رقم السجل التجاري الأردني <span class="text-red-600">*</span></label>
                <input type="text" name="commercial_registration_no" value="{{ old('commercial_registration_no') }}" required placeholder="CR-..." class="w-full text-xs font-mono rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">الرقم الضريبي (اختياري)</label>
                <input type="text" name="tax_number" value="{{ old('tax_number') }}" placeholder="TAX-..." class="w-full text-xs font-mono rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="border-b border-stone-100 pb-3 pt-2">
            <h3 class="text-sm font-black text-stone-900">بيانات الاتصال والموقع في الأردن</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">البريد الإلكتروني للعمليات <span class="text-red-600">*</span></label>
                <input type="email" name="contact_email" value="{{ old('contact_email', auth()->user()->email) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رقم الهاتف الأردني <span class="text-red-600">*</span></label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', auth()->user()->phone) }}" required placeholder="+9627..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">المحافظة <span class="text-red-600">*</span></label>
                <select name="governorate" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    <option value="Amman">عمّان</option>
                    <option value="Zarqa">الزرقاء</option>
                    <option value="Irbid">إربد</option>
                    <option value="Aqaba">العقبة</option>
                    <option value="Balqa">البلقاء / السلط</option>
                    <option value="Madaba">مأدبا</option>
                    <option value="Jerash">جرش</option>
                    <option value="Ajloun">عجلون</option>
                    <option value="Karak">الكرك</option>
                    <option value="Maan">معان</option>
                    <option value="Mafraq">المفرق</option>
                    <option value="Tafilah">الطفيلة</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">المدينة / الحي <span class="text-red-600">*</span></label>
                <input type="text" name="city" value="{{ old('city') }}" required placeholder="مثال: الجبيهة" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">عنوان المستودع أو المعرض</label>
                <input type="text" name="address_details" value="{{ old('address_details') }}" placeholder="الشارع ورقم البناية" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="border-b border-stone-100 pb-3 pt-2">
            <h3 class="text-sm font-black text-stone-900">بيانات التحويل البنكي (لتسوية الأرباح)</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">اسم البنك الأردني</label>
                <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="مثال: البنك العربي" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رقم الآيبان الدولي (IBAN)</label>
                <input type="text" name="iban" value="{{ old('iban') }}" placeholder="JO..." dir="ltr" class="w-full text-xs font-mono rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">اسم صاحب الحساب البنكي</label>
                <input type="text" name="account_holder_name" value="{{ old('account_holder_name') }}" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">نبذة عن النشاط والمنتجات التي ترغب ببيعها</label>
            <textarea name="description_ar" rows="3" placeholder="أخبرنا عن نوع البضائع، الوكالات التجارية، أو الماركات التي توفرها..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">{{ old('description_ar') }}</textarea>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end">
            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-amber-500 hover:bg-amber-400 text-stone-950 font-black text-sm rounded-xl shadow-xs transition-colors">
                تقديم طلب الانضمام للتدقيق
            </button>
        </div>
    </form>

</div>
@endsection
