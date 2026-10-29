@extends('layouts.admin')

@section('title', 'معالجة النزاع #' . $dispute->id . ' — الإدارة')

@section('content')
<div class="space-y-6 max-w-4xl text-xs">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.disputes.index') }}" class="text-stone-500 hover:text-stone-900 font-bold mb-1 inline-block">
                ← العودة لقائمة النزاعات
            </a>
            <h1 class="text-lg font-black text-stone-900">معالجة طلب الإرجاع والنزاع #{{ $dispute->id }}</h1>
            <p class="text-stone-400 font-mono text-[11px]">طلب الشحنة: {{ $dispute->sellerOrder->order_number }}</p>
        </div>

        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $dispute->status === 'refunded' || $dispute->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
            {{ $dispute->status }}
        </span>
    </div>

    <!-- Summary Box -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-white p-5 rounded-2xl border border-stone-200">
        <div>
            <span class="text-stone-400 block mb-0.5">العميل مقدم الطلب:</span>
            <span class="font-bold text-stone-900">{{ $dispute->user->name }}</span>
            <span class="text-stone-500 block text-[11px]">{{ $dispute->user->phone }}</span>
        </div>
        <div>
            <span class="text-stone-400 block mb-0.5">التاجر البائع:</span>
            <span class="font-bold text-stone-900">{{ app()->getLocale() === 'ar' ? $dispute->sellerOrder->seller->store_name_ar : $dispute->sellerOrder->seller->store_name_en }}</span>
            <span class="text-stone-500 block text-[11px]">عمولة التاجر: {{ $dispute->sellerOrder->commission_rate }}%</span>
        </div>
        <div>
            <span class="text-stone-400 block mb-0.5">المبلغ الإجمالي للشحنة:</span>
            <span class="font-black text-emerald-700 text-sm">{{ number_format($dispute->sellerOrder->total_amount, 3) }} JOD</span>
            <span class="text-stone-400 block text-[10px]">طريقة الدفع الأصلية: {{ $dispute->sellerOrder->parentOrder->payment_method }}</span>
        </div>
    </div>

    <!-- Customer Reason -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200 space-y-2">
        <h3 class="font-bold text-stone-900">سبب الإرجاع المذكور من العميل:</h3>
        <p class="p-3 bg-stone-50 rounded-xl border border-stone-200 text-stone-800 leading-relaxed">{{ $dispute->reason }}</p>
    </div>

    <!-- Items to return -->
    <div class="bg-white p-5 rounded-2xl border border-stone-200 space-y-3">
        <h3 class="font-bold text-stone-900 border-b border-stone-100 pb-2">المنتجات المطلوب إرجاعها:</h3>
        @foreach($dispute->items as $item)
            <div class="flex items-center justify-between p-3 rounded-xl border border-stone-200">
                <div>
                    <span class="font-bold text-stone-900 block">{{ app()->getLocale() === 'ar' ? $item->orderItem->product_title_ar : $item->orderItem->product_title_en }}</span>
                    <span class="text-stone-500 text-[11px]">الكمية: {{ $item->quantity }} • السعر: {{ number_format($item->orderItem->unit_price, 3) }} JOD</span>
                </div>
                <span class="font-black text-stone-900">{{ number_format((float) $item->orderItem->unit_price * $item->quantity, 3) }} JOD</span>
            </div>
        @endforeach
    </div>

    <!-- Actions Form -->
    <div class="bg-white p-6 rounded-2xl border border-stone-200 space-y-4">
        <h3 class="font-black text-stone-900 text-sm">إجراء الإدارة المالي والتشغيلي</h3>

        <form method="POST" action="{{ route('admin.disputes.process', $dispute->id) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-stone-800 mb-1">حدد الإجراء *</label>
                    <select name="action" class="w-full rounded-xl border border-stone-300 p-2.5 font-bold">
                        <option value="approve">1. الموافقة على طلب الإرجاع (بانتظار وصول المنتج)</option>
                        <option value="mark_received">2. تأكيد استلام المنتج وإعادته لمخزون التاجر</option>
                        <option value="process_refund">3. تنفيذ الاسترداد المالي وقيد الخصم على التاجر</option>
                        <option value="reject">رفض طلب الإرجاع</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-stone-800 mb-1">مبلغ الاسترداد (د.أ) - في حال الاسترداد المالي</label>
                    <input type="number" step="0.001" name="refund_amount" value="{{ (float) $dispute->sellerOrder->total_amount }}" class="w-full rounded-xl border border-stone-300 p-2.5 font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block font-bold text-stone-800 mb-1">ملاحظات الإدارة والقرار *</label>
                <textarea name="admin_notes" rows="3" placeholder="اكتب مبررات القرار أو أسباب الرفض أو مرجع التحويل المالي..." class="w-full rounded-xl border border-stone-300 p-3"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition-all shadow-xs">
                    تنفيذ الإجراء واعتماده
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
