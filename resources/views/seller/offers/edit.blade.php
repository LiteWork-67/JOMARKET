@extends('layouts.seller')

@section('title', 'تعديل عرض السعر — JordanMart')
@section('page_title', 'تعديل عرض السعر')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black text-stone-900">تعديل عرض: {{ $offer->product->name }}</h2>
            <p class="text-xs text-stone-500 mt-0.5">رمز SKU: {{ $offer->seller_sku }}</p>
        </div>
        <a href="{{ route('seller.offers.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة للعروض
        </a>
    </div>

    <form method="POST" action="{{ route('seller.offers.update', $offer->id) }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-5">
        @csrf
        @method('PUT')

        <div class="p-4 rounded-xl bg-stone-50 border border-stone-200 flex items-center gap-4">
            @if($offer->product->main_image_url)
                <img src="{{ $offer->product->main_image_url }}" alt="" class="w-14 h-14 rounded-lg object-cover border border-stone-200">
            @endif
            <div>
                <p class="text-xs font-black text-stone-900">{{ $offer->product->name }}</p>
                <p class="text-[11px] text-stone-500">{{ $offer->product->category?->name }} • {{ $offer->product->brand?->name }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رمز المخزون (SKU) <span class="text-red-600">*</span></label>
                <input type="text" name="seller_sku" value="{{ old('seller_sku', $offer->seller_sku) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">حالة المنتج <span class="text-red-600">*</span></label>
                <select name="condition" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    <option value="new" {{ old('condition', $offer->condition) === 'new' ? 'selected' : '' }}>جديد بكرتون المصنع (New)</option>
                    <option value="refurbished" {{ old('condition', $offer->condition) === 'refurbished' ? 'selected' : '' }}>مجدد معتمد (Refurbished)</option>
                    <option value="used" {{ old('condition', $offer->condition) === 'used' ? 'selected' : '' }}>مستعمل بحالة ممتازة (Used)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">السعر الأساسي (دينار أردني JOD) <span class="text-red-600">*</span></label>
                <input type="number" step="0.001" name="price" value="{{ old('price', $offer->price) }}" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">سعر العرض الترويجي (اختياري)</label>
                <input type="number" step="0.001" name="sale_price" value="{{ old('sale_price', $offer->sale_price) }}" placeholder="0.000" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">الكمية المتوفرة بالمخزون <span class="text-red-600">*</span></label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $offer->stock_quantity) }}" min="0" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">أيام التجهيز</label>
                <input type="number" name="handling_days" value="{{ old('handling_days', $offer->handling_days) }}" min="0" max="30" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">أقصى كمية للطلب</label>
                <input type="number" name="max_order_quantity" value="{{ old('max_order_quantity', $offer->max_order_quantity) }}" min="1" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-400 border-stone-300">
                <span class="text-xs font-bold text-stone-800">العرض نشط ومعروض للمشترين بالسوق</span>
            </label>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
            <a href="{{ route('seller.offers.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">إلغاء</a>
            <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors">
                حفظ التعديلات
            </button>
        </div>
    </form>

</div>
@endsection
