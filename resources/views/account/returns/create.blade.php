@extends('account.layout')

@section('title', 'طلب إرجاع للمتجر — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-xs max-w-2xl space-y-6">
    <div class="border-b border-stone-100 pb-4">
        <a href="{{ route('account.orders.show', $sellerOrder->parentOrder->order_number) }}" class="text-xs text-stone-500 hover:text-stone-900 font-bold mb-2 inline-block">
            ← العودة لتفاصيل الطلب
        </a>
        <h2 class="text-sm font-black text-stone-900">
            طلب إرجاع لطلب الشحنة: {{ $sellerOrder->order_number }}
        </h2>
        <p class="text-xs text-stone-500 mt-0.5">
            المتجر: {{ app()->getLocale() === 'ar' ? $sellerOrder->seller->store_name_ar : $sellerOrder->seller->store_name_en }}
        </p>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('account.returns.store', $sellerOrder->id) }}" class="space-y-5 text-xs">
        @csrf

        <!-- Items to Return -->
        <div class="space-y-3">
            <label class="block font-bold text-stone-800">حدد المنتجات المراد إرجاعها والكمية:</label>
            @foreach($sellerOrder->items as $index => $item)
                <div class="p-3.5 rounded-xl border border-stone-200 bg-stone-50 flex items-center justify-between gap-4">
                    <input type="hidden" name="items[{{ $index }}][order_item_id]" value="{{ $item->id }}">
                    <div class="flex-1 min-w-0">
                        <span class="font-bold text-stone-900 block truncate">
                            {{ app()->getLocale() === 'ar' ? $item->product_title_ar : $item->product_title_en }}
                        </span>
                        <span class="text-[11px] text-stone-500">
                            الكمية المشتراة: {{ $item->quantity }} • السعر: {{ number_format($item->unit_price, 3) }} JOD
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-semibold text-stone-600">كمية الإرجاع:</label>
                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" max="{{ $item->quantity }}" class="w-16 rounded-lg border border-stone-300 p-1.5 text-center font-bold">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Resolution Type -->
        <div>
            <label class="block font-bold text-stone-800 mb-1">نوع المعالجة المطلوب:</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="p-3 rounded-xl border border-stone-200 cursor-pointer hover:bg-stone-50 flex items-center gap-2">
                    <input type="radio" name="resolution_type" value="refund" checked class="text-amber-500">
                    <span class="font-bold text-stone-900">استرداد المبلغ نقداً / بنكياً</span>
                </label>
                <label class="p-3 rounded-xl border border-stone-200 cursor-pointer hover:bg-stone-50 flex items-center gap-2">
                    <input type="radio" name="resolution_type" value="exchange" class="text-amber-500">
                    <span class="font-bold text-stone-900">استبدال بمنتج سليم / مقاس آخر</span>
                </label>
            </div>
        </div>

        <!-- Return Reason -->
        <div>
            <label class="block font-bold text-stone-800 mb-1">سبب طلب الإرجاع بالتفصيل *</label>
            <textarea name="reason" rows="3" required placeholder="يرجى ذكر سبب الإرجاع (مثال: المنتج لا يطابق المواصفات، به عيب مصنعي، مقاس غير مناسب)..." class="w-full rounded-xl border border-stone-300 p-3 focus:border-stone-900"></textarea>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-black text-xs shadow-sm transition-all">
                إرسال طلب الإرجاع
            </button>
        </div>
    </form>
</div>
@endsection
