@extends('account.layout')

@section('title', 'تفاصيل طلب الإرجاع #' . $returnRequest->id . ' — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-xs max-w-2xl space-y-6 text-xs">
    <div class="border-b border-stone-100 pb-4">
        <a href="{{ route('account.returns.index') }}" class="text-stone-500 hover:text-stone-900 font-bold mb-2 inline-block">
            ← العودة لقائمة طلبات الإرجاع
        </a>
        <div class="flex items-center justify-between">
            <h1 class="text-base font-black text-stone-900 font-mono">طلب إرجاع #{{ $returnRequest->id }}</h1>
            <span class="px-2.5 py-1 rounded-full font-bold text-[10px] {{ $returnRequest->status === 'approved' || $returnRequest->status === 'refunded' ? 'bg-emerald-100 text-emerald-800' : ($returnRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                {{ $returnRequest->status }}
            </span>
        </div>
        <p class="text-stone-500 mt-1">تاريخ الطلب: {{ $returnRequest->created_at->format('Y-m-d H:i') }}</p>
    </div>

    <!-- Details -->
    <div class="grid grid-cols-2 gap-4 bg-stone-50 p-4 rounded-xl">
        <div>
            <span class="text-stone-400 block mb-0.5">شحنة التاجر:</span>
            <span class="font-bold text-stone-900">{{ $returnRequest->sellerOrder->order_number }}</span>
            <span class="text-stone-500 block text-[11px]">{{ app()->getLocale() === 'ar' ? $returnRequest->sellerOrder->seller->store_name_ar : $returnRequest->sellerOrder->seller->store_name_en }}</span>
        </div>
        <div>
            <span class="text-stone-400 block mb-0.5">نوع التسوية:</span>
            <span class="font-bold text-stone-900">{{ $returnRequest->resolution_type === 'refund' ? 'استرداد مالي' : 'استبدال' }}</span>
        </div>
    </div>

    <!-- Items -->
    <div class="space-y-3">
        <h3 class="font-bold text-stone-900 border-b border-stone-100 pb-2">المنتجات المشمولة بالإرجاع:</h3>
        @foreach($returnRequest->items as $item)
            <div class="p-3 rounded-xl border border-stone-200 flex items-center justify-between">
                <div>
                    <span class="font-bold text-stone-900 block">
                        {{ app()->getLocale() === 'ar' ? $item->orderItem->product_title_ar : $item->orderItem->product_title_en }}
                    </span>
                    <span class="text-stone-500 text-[11px]">الكمية المعادة: {{ $item->quantity }}</span>
                </div>
                <span class="font-black text-stone-900">
                    {{ number_format((float) $item->orderItem->unit_price * $item->quantity, 3) }} JOD
                </span>
            </div>
        @endforeach
    </div>

    <!-- Reason -->
    <div>
        <h3 class="font-bold text-stone-900 mb-1">سبب طلب الإرجاع:</h3>
        <p class="p-3 rounded-xl bg-stone-50 border border-stone-200 text-stone-700 leading-relaxed">{{ $returnRequest->reason }}</p>
    </div>

    @if($returnRequest->admin_notes)
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900">
            <span class="font-bold block mb-1">ملاحظات خدمة العملاء والإدارة:</span>
            <p>{{ $returnRequest->admin_notes }}</p>
        </div>
    @endif
</div>
@endsection
