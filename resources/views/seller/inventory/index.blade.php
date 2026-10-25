@extends('layouts.seller')

@section('title', 'إدارة المخزون والتوريد — JordanMart')
@section('page_title', 'إدارة المخزون والتوريد')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">مستويات المخزون وحركات التوريد</h2>
            <p class="text-xs text-stone-500 mt-0.5">تتبع أرصدة بضائعك، تحديث الكميات عند التوريد، ومراقبة المخزون المحجوز للطلبات النشطة.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('seller.inventory.index', ['low_stock' => 1]) }}" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs rounded-xl border border-red-200 transition-colors">
                ⚠️ عرض ما أوشك على النفاد (<= 5)
            </a>
            @if(request()->filled('low_stock') || request()->filled('search'))
                <a href="{{ route('seller.inventory.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                    كافة العروض
                </a>
            @endif
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">مخزون المنتجات النشطة</h3>
            <form method="GET" action="{{ route('seller.inventory.index') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المنتج أو SKU..." class="text-xs rounded-xl border-stone-200 px-3 py-1.5 bg-stone-50">
                <button type="submit" class="px-3 py-1.5 bg-stone-900 text-white rounded-xl text-xs font-bold">بحث</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">المنتج</th>
                        <th class="py-3 px-4 font-bold text-start">رمز المخزون (SKU)</th>
                        <th class="py-3 px-4 font-bold text-start">المخزون الإجمالي</th>
                        <th class="py-3 px-4 font-bold text-start">المحجوز لطلبات</th>
                        <th class="py-3 px-4 font-bold text-start">المتاح للبيع</th>
                        <th class="py-3 px-4 font-bold text-center">تعديل المخزون (توريد / تسوية)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($offers as $offer)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    @if($offer->product->main_image_url)
                                        <img src="{{ $offer->product->main_image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-stone-200 shrink-0">
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-bold text-stone-900 line-clamp-1">{{ $offer->product->name }}</p>
                                        <p class="text-[10px] text-stone-400">{{ $offer->product->category?->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $offer->seller_sku }}</td>
                            <td class="py-3 px-4">
                                <span class="font-black text-sm {{ $offer->stock_quantity <= 5 ? 'text-red-600' : 'text-stone-900' }}">
                                    {{ $offer->stock_quantity }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-medium text-amber-600">{{ $offer->reserved_quantity }}</td>
                            <td class="py-3 px-4 font-bold text-emerald-600">{{ $offer->available_stock }}</td>
                            <td class="py-3 px-4">
                                <form method="POST" action="{{ route('seller.inventory.update', $offer->id) }}" class="flex items-center justify-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="adjustment" placeholder="+10 أو -5" required class="w-24 text-xs rounded-lg border-stone-200 px-2 py-1 text-center bg-stone-50 focus:bg-white">
                                    <input type="text" name="reason" placeholder="سبب التسوية (مثال: شحنة جديدة)" required class="w-40 text-xs rounded-lg border-stone-200 px-2 py-1 bg-stone-50 focus:bg-white">
                                    <button type="submit" class="px-3 py-1 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold rounded-lg text-xs transition-colors shrink-0">
                                        تطبيق
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-stone-400">لا توجد منتجات مسجلة بالمخزون.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($offers->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $offers->links() }}
            </div>
        @endif
    </div>

    <!-- Inventory Movement History -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-stone-100">
            <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">سجل آخر حركات المخزون والتوريد</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-2.5 px-4 font-bold text-start">المنتج</th>
                        <th class="py-2.5 px-4 font-bold text-start">النوع</th>
                        <th class="py-2.5 px-4 font-bold text-start">الكمية المعدلة</th>
                        <th class="py-2.5 px-4 font-bold text-start">الرصيد بعد التعديل</th>
                        <th class="py-2.5 px-4 font-bold text-start">ملاحظات وسبب التعديل</th>
                        <th class="py-2.5 px-4 font-bold text-start">التاريخ والمستخدم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($movements as $mov)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-2.5 px-4 font-bold text-stone-900">{{ $mov->offer?->product?->name }}</td>
                            <td class="py-2.5 px-4 font-medium">{{ $mov->type }}</td>
                            <td class="py-2.5 px-4 font-black {{ $mov->quantity > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $mov->quantity > 0 ? '+' . $mov->quantity : $mov->quantity }}
                            </td>
                            <td class="py-2.5 px-4 font-bold">{{ $mov->balance_after }}</td>
                            <td class="py-2.5 px-4 text-stone-500">{{ $mov->notes }}</td>
                            <td class="py-2.5 px-4 text-stone-400 text-[11px]">{{ $mov->created_at->format('Y-m-d H:i') }} ({{ $mov->createdByUser?->name }})</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-stone-400">لا توجد حركات مخزون مسجلة حديثاً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
