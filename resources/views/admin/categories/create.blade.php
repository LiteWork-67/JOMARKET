@extends('layouts.admin')

@section('title', 'إضافة تصنيف جديد — JordanMart')
@section('page_title', 'إضافة تصنيف جديد')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black text-stone-900">إنشاء قسم أو تصنيف بالكتالوج</h2>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة للتصنيفات
        </a>
    </div>

    <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">الاسم بالعربية <span class="text-red-600">*</span></label>
                <input type="text" name="name_ar" value="{{ old('name_ar') }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">الاسم بالإنجليزية <span class="text-red-600">*</span></label>
                <input type="text" name="name_en" value="{{ old('name_en') }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">الرابط اللطيف (Slug) <span class="text-red-600">*</span></label>
                <input type="text" name="slug" value="{{ old('slug') }}" required placeholder="electronics-mobiles" dir="ltr" class="w-full text-xs font-mono rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">القسم الرئيسي (إذا كان قسماً فرعياً)</label>
                <select name="parent_id" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">-- بدون قسم رئيسي (قسم رئيسي مستقل) --</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name_ar }} ({{ $parent->name_en }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">نسبة العمولة الافتراضية (%)</label>
                <input type="number" step="0.1" name="commission_rate" value="{{ old('commission_rate') }}" placeholder="10.0" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رمز الأيقونة (Heroicons/Lucide)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="device-phone-mobile" dir="ltr" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">وصف القسم</label>
            <textarea name="description_ar" rows="2" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">{{ old('description_ar') }}</textarea>
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded text-red-600 focus:ring-red-500 border-stone-300">
                <span class="text-xs font-bold text-stone-800">تفعيل القسم فوراً في الواجهة والمتجر</span>
            </label>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">إلغاء</a>
            <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-500 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                حفظ التصنيف
            </button>
        </div>
    </form>

</div>
@endsection
