@extends('layouts.admin')

@section('title', 'تدقيق المنتج: ' . $product->name . ' — JordanMart')
@section('page_title', 'تدقيق واعتماد المنتج')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-stone-900">{{ $product->name }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold
                    @if($product->status === 'approved') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif($product->status === 'pending') bg-amber-50 text-amber-700 border border-amber-200
                    @elseif($product->status === 'rejected') bg-red-50 text-red-700 border border-red-200
                    @endif">
                    {{ $product->status }}
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">الرابط الدائم: /product/{{ $product->slug }} • الباركود: {{ $product->barcode ?: '—' }}</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة لقائمة المنتجات
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Product Details & Offers -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Catalog Spec -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-start gap-5">
                    @if($product->main_image_url)
                        <img src="{{ $product->main_image_url }}" alt="" class="w-24 h-24 rounded-xl object-cover border border-stone-200 shrink-0">
                    @endif
                    <div class="space-y-1">
                        <span class="text-xs text-stone-400 font-mono">{{ $product->brand?->name }} • {{ $product->category?->name }}</span>
                        <h3 class="text-base font-black text-stone-900">{{ $product->name_ar }}</h3>
                        <p class="text-xs font-medium text-stone-600" dir="ltr">{{ $product->name_en }}</p>
                    </div>
                </div>

                <div class="border-t border-stone-100 pt-4 space-y-3 text-xs">
                    <div>
                        <span class="font-bold text-stone-700">الوصف بالعربية:</span>
                        <p class="text-stone-600 mt-1 leading-relaxed">{{ $product->description_ar ?: 'لا يوجد وصف مدخل' }}</p>
                    </div>
                    @if($product->description_en)
                        <div>
                            <span class="font-bold text-stone-700">الوصف بالإنجليزية:</span>
                            <p class="text-stone-600 mt-1 leading-relaxed" dir="ltr">{{ $product->description_en }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seller Offers on this product -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-stone-100">
                    <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">عروض التجار المرتبطة بهذا المنتج</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-start text-xs">
                        <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                            <tr>
                                <th class="py-2.5 px-4 font-bold text-start">التاجر / المتجر</th>
                                <th class="py-2.5 px-4 font-bold text-start">رمز SKU</th>
                                <th class="py-2.5 px-4 font-bold text-start">السعر الأساسي</th>
                                <th class="py-2.5 px-4 font-bold text-start">سعر التخفيض</th>
                                <th class="py-2.5 px-4 font-bold text-start">المخزون</th>
                                <th class="py-2.5 px-4 font-bold text-start">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 text-stone-700">
                            @forelse($product->offers as $offer)
                                <tr>
                                    <td class="py-2.5 px-4 font-bold text-stone-900">{{ $offer->seller?->store_name }}</td>
                                    <td class="py-2.5 px-4 font-mono">{{ $offer->seller_sku }}</td>
                                    <td class="py-2.5 px-4">{{ number_format($offer->price, 3) }} د.أ</td>
                                    <td class="py-2.5 px-4 text-red-600 font-bold">{{ $offer->sale_price ? number_format($offer->sale_price, 3) . ' د.أ' : '—' }}</td>
                                    <td class="py-2.5 px-4 font-bold">{{ $offer->stock_quantity }}</td>
                                    <td class="py-2.5 px-4">
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold {{ $offer->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-100 text-stone-600' }}">
                                            {{ $offer->is_active ? 'نشط' : 'موقف' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-stone-400">لا توجد عروض أسعار من التجار على هذا المنتج حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Col: Moderation Form -->
        <div class="space-y-6">

            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-4">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider border-b border-stone-100 pb-2">قرار الاعتماد المركزي</h3>

                <!-- Approve Form -->
                <form method="POST" action="{{ route('admin.products.moderate', $product->id) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="action" value="approve">

                    <p class="text-xs text-stone-600">اعتماد المنتج يجعله مرئياً في الكتالوج العام ويمكّن التجار من تقديم عروض أسعار عليه.</p>

                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-xs transition-colors">
                        ✓ اعتماد ونشر بالكتالوج العام
                    </button>
                </form>

                <!-- Reject Form -->
                <form method="POST" action="{{ route('admin.products.moderate', $product->id) }}" class="space-y-3 pt-3 border-t border-stone-100">
                    @csrf
                    <input type="hidden" name="action" value="reject">

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">سبب الرفض (إلزامي عند الرفض)</label>
                        <textarea name="rejection_reason" rows="2" placeholder="أدخل سبب عدم مطابقة المنتج..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">{{ $product->rejection_reason }}</textarea>
                    </div>

                    <button type="submit" onclick="return confirm('هل أنت متأكد من رفض هذا المنتج؟');" class="w-full py-2.5 bg-stone-100 hover:bg-red-50 hover:text-red-700 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        ✕ رفض المنتج
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
