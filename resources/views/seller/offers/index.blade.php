@extends('layouts.seller')

@section('title', 'عروض المتجر والأسعار — JordanMart')
@section('page_title', 'عروض المتجر والأسعار')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">قائمة عروضك المعروضة بالسوق</h2>
            <p class="text-xs text-stone-500 mt-0.5">يمكنك إضافة عروض على المنتجات المعتمدة بالسوق، وضبط أسعار البيع والتخفيضات ومستويات التوفر.</p>
        </div>
        <a href="{{ route('seller.offers.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>إضافة عرض جديد</span>
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('seller.offers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المنتج أو رمز SKU..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    <option value="">كافة الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط ومعروض</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط / موقف</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('seller.offers.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        إعادة ضبط
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Offers Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">المنتج</th>
                        <th class="py-3 px-4 font-bold text-start">رمز SKU الخاص بك</th>
                        <th class="py-3 px-4 font-bold text-start">السعر الأساسي</th>
                        <th class="py-3 px-4 font-bold text-start">سعر العرض</th>
                        <th class="py-3 px-4 font-bold text-start">المخزون المتوفر</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
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
                                        <p class="text-[10px] text-stone-400">{{ $offer->product->category?->name }} • {{ $offer->product->brand?->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono font-medium text-stone-800">{{ $offer->seller_sku }}</td>
                            <td class="py-3 px-4 font-semibold">{{ number_format($offer->price, 3) }} د.أ</td>
                            <td class="py-3 px-4 font-bold {{ $offer->hasDiscount() ? 'text-red-600' : 'text-stone-500' }}">
                                @if($offer->hasDiscount())
                                    {{ number_format($offer->sale_price, 3) }} د.أ
                                    <span class="block text-[10px] text-red-500 font-medium">وفر {{ $offer->discount_percentage }}%</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold {{ $offer->stock_quantity <= 5 ? 'text-red-600' : 'text-stone-900' }}">
                                    {{ $offer->stock_quantity }}
                                </span>
                                @if($offer->reserved_quantity > 0)
                                    <span class="text-[10px] text-stone-400">({{ $offer->reserved_quantity }} محجوز)</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $offer->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-stone-100 text-stone-600 border border-stone-200' }}">
                                    {{ $offer->is_active ? 'نشط' : 'موقف' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('seller.offers.edit', $offer->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                        تعديل
                                    </a>
                                    @if($offer->is_active)
                                        <form method="POST" action="{{ route('seller.offers.destroy', $offer->id) }}" onsubmit="return confirm('هل أنت متأكد من إيقاف هذا العرض؟');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold rounded-lg text-xs transition-colors">
                                                إيقاف
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد عروض مضافة حالياً. اضغط "إضافة عرض جديد" للبدء.</td>
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

</div>
@endsection
