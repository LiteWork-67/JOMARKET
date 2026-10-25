@extends('layouts.seller')

@section('title', 'إضافة عرض جديد — JordanMart')
@section('page_title', 'إضافة عرض جديد')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black text-stone-900">إضافة عرض سعر لمنتج معتمد بالسوق</h2>
            <p class="text-xs text-stone-500 mt-0.5">حدد المنتج من الكتالوج العام، وضع السعر والمخزون الخاص بمتجرك.</p>
        </div>
        <a href="{{ route('seller.offers.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة للعروض
        </a>
    </div>

    <form method="POST" action="{{ route('seller.offers.store') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-5">
        @csrf

        <!-- Product Selector -->
        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">اختر المنتج من الكتالوج <span class="text-red-600">*</span></label>
            <select name="product_id" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2.5 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                <option value="">-- حدد المنتج --</option>
                @foreach($products as $prod)
                    <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                        {{ $prod->name }} ({{ $prod->category?->name }})
                    </option>
                @endforeach
            </select>
            <p class="text-[11px] text-stone-400 mt-1">يتم عرض المنتجات المعتمدة من إدارة المنصة فقط.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- SKU -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">رمز المخزون الخاص بك (SKU) <span class="text-red-600">*</span></label>
                <input type="text" name="seller_sku" value="{{ old('seller_sku') }}" required placeholder="مثال: HUB-IPH-256-BLK" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <!-- Condition -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">حالة المنتج <span class="text-red-600">*</span></label>
                <select name="condition" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>جديد بكرتون المصنع (New)</option>
                    <option value="refurbished" {{ old('condition') === 'refurbished' ? 'selected' : '' }}>مجدد معتمد (Refurbished)</option>
                    <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>مستعمل بحالة ممتازة (Used)</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Regular Price -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">السعر الأساسي (بالدينار الأردني JOD) <span class="text-red-600">*</span></label>
                <input type="number" step="0.001" name="price" value="{{ old('price') }}" required placeholder="0.000" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <!-- Sale Price -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">سعر العرض الترويجي (اختياري)</label>
                <input type="number" step="0.001" name="sale_price" value="{{ old('sale_price') }}" placeholder="0.000" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                <p class="text-[10px] text-stone-400 mt-0.5">يجب أن يكون أقل من السعر الأساسي إذا تم تحديده.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Stock Quantity -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">الكمية المتوفرة بالمستودع <span class="text-red-600">*</span></label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 1) }}" min="0" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <!-- Handling Days -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">أيام التجهيز للشحن</label>
                <input type="number" name="handling_days" value="{{ old('handling_days', 1) }}" min="0" max="30" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>

            <!-- Max Order Quantity -->
            <div>
                <label class="block text-xs font-bold text-stone-700 mb-1">أقصى كمية للطلب الواحد</label>
                <input type="number" name="max_order_quantity" value="{{ old('max_order_quantity', 5) }}" min="1" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
        </div>

        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded text-amber-500 focus:ring-amber-400 border-stone-300">
                <span class="text-xs font-bold text-stone-800">تفعيل ونشر العرض مباشرة في السوق</span>
            </label>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
            <a href="{{ route('seller.offers.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">إلغاء</a>
            <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors">
                حفظ ونشر العرض
            </button>
        </div>
    </form>

</div>
@endsection
