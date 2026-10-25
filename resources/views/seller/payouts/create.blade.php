@extends('layouts.seller')

@section('title', 'طلب سحب أرباح جديدة — JordanMart')
@section('page_title', 'طلب سحب أرباح جديدة')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-black text-stone-900">طلب تحويل بنكي للمستحقات</h2>
            <p class="text-xs text-stone-500 mt-0.5">سيتم مراجعة الطلب وتحويل المبلغ إلى حسابك البنكي المعتمد.</p>
        </div>
        <a href="{{ route('seller.payouts.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة للسجل
        </a>
    </div>

    <!-- Balance Status -->
    <div class="bg-amber-500/10 border border-amber-500/30 rounded-2xl p-4 flex items-center justify-between">
        <div>
            <span class="text-xs text-amber-800 font-semibold">الرصيد المتاح للسحب الآن:</span>
            <div class="text-2xl font-black text-amber-900 mt-0.5">
                {{ number_format($availableForPayout, 3) }} <span class="text-xs font-bold">د.أ</span>
            </div>
        </div>
        <div class="text-end text-[11px] text-amber-700">
            <span>الرصيد الكلي: {{ number_format($settledBalance, 3) }} د.أ</span>
        </div>
    </div>

    <!-- Payout Form -->
    <form method="POST" action="{{ route('seller.payouts.store') }}" class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-6 space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">المبلغ المطلوب سحبه (دينار أردني JOD) <span class="text-red-600">*</span></label>
            <input type="number" step="0.001" name="amount" value="{{ old('amount', $availableForPayout) }}" min="1" max="{{ $availableForPayout }}" required class="w-full text-base font-bold rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            <p class="text-[11px] text-stone-400 mt-1">الحد الأدنى للتحويل هو 1.000 د.أ.</p>
        </div>

        <!-- Bank details summary -->
        <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600 space-y-0.5">
            <p class="font-bold text-stone-800">بيانات التحويل المسجلة:</p>
            <p>البنك: {{ $seller->bank_name ?: 'غير مسجل' }}</p>
            <p>الآيبان: <span dir="ltr" class="font-mono">{{ $seller->iban ?: 'غير مسجل' }}</span></p>
            <p>اسم المستفيد: {{ $seller->account_holder_name ?: $seller->store_name }}</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-stone-700 mb-1">ملاحظات التحويل (اختياري)</label>
            <textarea name="notes" rows="2" placeholder="أي تفاصيل ترغب بإيصالها لقسم المحاسبة..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">{{ old('notes') }}</textarea>
        </div>

        <div class="pt-4 border-t border-stone-100 flex items-center justify-end gap-3">
            <a href="{{ route('seller.payouts.index') }}" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">إلغاء</a>
            <button type="submit" class="px-6 py-2 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors">
                تأكيد وإرسال طلب السحب
            </button>
        </div>
    </form>

</div>
@endsection
