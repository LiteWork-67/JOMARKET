@extends('account.layout')

@section('title', 'فتح تذكرة دعم فني جديدة — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-xs max-w-xl space-y-6">
    <div class="border-b border-stone-100 pb-4">
        <a href="{{ route('account.tickets.index') }}" class="text-xs text-stone-500 hover:text-stone-900 font-bold mb-2 inline-block">
            ← العودة لقائمة التذاكر
        </a>
        <h2 class="text-sm font-black text-stone-900">فتح تذكرة دعم واستفسار جديدة</h2>
        <p class="text-xs text-stone-500 mt-0.5">سيسعد ممثلو خدمة العملاء بمساعدتك والإجابة على أي استفسار.</p>
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

    <form method="POST" action="{{ route('account.tickets.store') }}" class="space-y-4 text-xs">
        @csrf

        <!-- Subject -->
        <div>
            <label class="block font-bold text-stone-700 mb-1">موضوع التذكرة *</label>
            <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="مثال: استفسار بخصوص موعد توصيل الطلب أو الدفع" class="w-full rounded-xl border border-stone-300 p-2.5 focus:border-stone-900">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Priority -->
            <div>
                <label class="block font-bold text-stone-700 mb-1">درجة الأهمية *</label>
                <select name="priority" required class="w-full rounded-xl border border-stone-300 p-2.5">
                    <option value="low">منخفضة (استفسار عام)</option>
                    <option value="medium" selected>متوسطة (طلب عادي)</option>
                    <option value="high">عالية (تأخير شحنة أو مشكلة دفع)</option>
                    <option value="urgent">طارئة جداً</option>
                </select>
            </div>

            <!-- Related Order (Optional) -->
            <div>
                <label class="block font-bold text-stone-700 mb-1">الطلب المرتبط (اختياري)</label>
                <select name="parent_order_id" class="w-full rounded-xl border border-stone-300 p-2.5">
                    <option value="">-- بدون طلب محدد --</option>
                    @foreach($userOrders as $order)
                        <option value="{{ $order->id }}" {{ $selectedOrderId == $order->id ? 'selected' : '' }}>
                            {{ $order->order_number }} ({{ $order->created_at->format('Y-m-d') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Message -->
        <div>
            <label class="block font-bold text-stone-700 mb-1">تفاصيل المشكلة أو الاستفسار *</label>
            <textarea name="message" rows="4" required placeholder="اشرح تفاصيل مشكلتك وسيقوم فريق الدعم بمراجعتها ومتابعتها..." class="w-full rounded-xl border border-stone-300 p-3 focus:border-stone-900">{{ old('message') }}</textarea>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-black text-xs shadow-sm transition-all">
                إرسال التذكرة
            </button>
        </div>
    </form>
</div>
@endsection
