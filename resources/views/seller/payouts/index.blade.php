@extends('layouts.seller')

@section('title', 'المستحقات وسحب الأرباح — JordanMart')
@section('page_title', 'المستحقات وسحب الأرباح')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">المستحقات المالية والتسويات البنكية</h2>
            <p class="text-xs text-stone-500 mt-0.5">متابعة الأرباح المحققة بعد اقتطاع عمولة المنصة وطلب التحويل إلى حسابك البنكي المعتمد بالأردن.</p>
        </div>
        @if($availableForPayout > 0)
            <a href="{{ route('seller.payouts.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center justify-center gap-1.5 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>طلب سحب أرباح جديدة</span>
            </a>
        @endif
    </div>

    <!-- Settlement Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي الرصيد المسوى بالدفتر</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ number_format($settledBalance, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">صافي أرباح الطلبات المسلمة</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">طلبات سحب قيد المعالجة</span>
            <div class="text-2xl font-black text-amber-600 mt-1">
                {{ number_format($pendingPayoutsSum, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">بانتظار موافقة الإدارة والتحويل</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">الرصيد المتاح للطلب فوراً</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                {{ number_format($availableForPayout, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">جاهز للتحويل البنكي</p>
        </div>
    </div>

    <!-- Bank Details Snapshot -->
    <div class="bg-stone-50 rounded-2xl p-5 border border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="text-xs">
            <p class="font-bold text-stone-900">بيانات الحساب البنكي المعتمد للتحويلات:</p>
            <p class="text-stone-600 mt-1">
                البنك: <span class="font-semibold text-stone-900">{{ $seller->bank_name ?: 'غير محدد' }}</span> • 
                الآيبان IBAN: <span class="font-mono font-bold text-stone-900" dir="ltr">{{ $seller->iban ?: 'غير مسجل' }}</span> • 
                المستفيد: <span class="font-semibold text-stone-900">{{ $seller->account_holder_name ?: $seller->store_name }}</span>
            </p>
        </div>
        <a href="{{ route('seller.profile.edit') }}" class="text-xs font-bold text-amber-700 hover:text-amber-800 underline shrink-0">
            تعديل الحساب البنكي ←
        </a>
    </div>

    <!-- Payout Requests History -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-stone-100">
            <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">سجل طلبات التحويل والسحب</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">المبلغ المطلوب</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-start">تاريخ الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">تاريخ المعالجة</th>
                        <th class="py-3 px-4 font-bold text-start">المرجع البنكي</th>
                        <th class="py-3 px-4 font-bold text-start">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-mono font-bold">#{{ $payout->id }}</td>
                            <td class="py-3 px-4 font-black text-stone-900">{{ number_format($payout->amount, 3) }} د.أ</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold
                                    @if($payout->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif(in_array($payout->status, ['requested', 'processing'])) bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($payout->status === 'rejected') bg-red-50 text-red-700 border border-red-200
                                    @else bg-stone-100 text-stone-700 @endif">
                                    {{ $payout->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">{{ $payout->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-stone-500">{{ $payout->processed_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td class="py-3 px-4 font-mono text-[11px] text-stone-600">{{ $payout->transaction_reference ?? '—' }}</td>
                            <td class="py-3 px-4 text-stone-500 max-w-xs truncate">
                                @if($payout->rejection_reason)
                                    <span class="text-red-600">مرفوض: {{ $payout->rejection_reason }}</span>
                                @else
                                    {{ $payout->notes ?? '—' }}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد طلبات سحب أرباح سابقة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payouts->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $payouts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
