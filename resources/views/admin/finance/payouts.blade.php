@extends('layouts.admin')

@section('title', 'طلبات تحويل الأرباح — JordanMart')
@section('page_title', 'طلبات تحويل الأرباح للتجار')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">طلبات السحب والتسويات البنكية</h2>
            <p class="text-xs text-stone-500 mt-0.5">التدقيق المالي لطلبات سحب الأرباح المقدمة من التجار، واعتماد التحويل أو رفضه مع تحديث السجل المالي.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.finance.payouts') }}" class="flex items-center gap-3">
            <select name="status" class="text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                <option value="">كافة الحالات</option>
                <option value="requested" {{ request('status') === 'requested' ? 'selected' : '' }}>بانتظار التحويل (Requested)</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>تمت الموافقة والتحويل (Completed)</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                تصفية
            </button>
            @if(request()->filled('status'))
                <a href="{{ route('admin.finance.payouts') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                    إلغاء التصفية
                </a>
            @endif
        </form>
    </div>

    <!-- Payout Requests Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">التاجر / المتجر</th>
                        <th class="py-3 px-4 font-bold text-start">بيانات البنك والآيبان</th>
                        <th class="py-3 px-4 font-bold text-start">المبلغ المطلوب</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-start">تاريخ الطلب</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات الاعتماد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">#{{ $payout->id }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-stone-900">{{ $payout->seller?->store_name }}</span>
                                <span class="block text-[10px] text-stone-400">بواسطة: {{ $payout->requestedByUser?->name }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-stone-800">{{ $payout->seller?->bank_name ?: 'غير مسجل' }}</span>
                                <span class="block font-mono text-[10px] text-stone-500" dir="ltr">{{ $payout->seller?->iban }}</span>
                            </td>
                            <td class="py-3 px-4 font-black text-stone-900 text-sm">{{ number_format($payout->amount, 3) }} د.أ</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                                    @if($payout->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif(in_array($payout->status, ['requested', 'processing'])) bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($payout->status === 'rejected') bg-red-50 text-red-700 border border-red-200
                                    @endif">
                                    {{ $payout->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">{{ $payout->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-center">
                                @if(in_array($payout->status, ['requested', 'processing']))
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Approve Form -->
                                        <form method="POST" action="{{ route('admin.finance.payouts.approve', $payout->id) }}" onsubmit="return confirm('تأكيد تحويل {{ number_format($payout->amount, 3) }} د.أ وخصمها من دفتر حساب التاجر؟');" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg text-xs transition-colors">
                                                ✓ اعتماد وتحويل
                                            </button>
                                        </form>

                                        <!-- Reject Form -->
                                        <form method="POST" action="{{ route('admin.finance.payouts.reject', $payout->id) }}" onsubmit="var reason = prompt('أدخل سبب رفض طلب السحب:'); if(!reason) return false; this.rejection_reason.value = reason; return true;" class="inline">
                                            @csrf
                                            <input type="hidden" name="rejection_reason" value="">
                                            <button type="submit" class="px-2.5 py-1 bg-stone-100 hover:bg-red-50 hover:text-red-700 text-stone-700 font-bold rounded-lg text-xs transition-colors">
                                                ✕ رفض
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <div class="text-[10px] text-stone-500">
                                        @if($payout->status === 'completed')
                                            <span class="text-emerald-700 font-medium">مرجع: {{ $payout->transaction_reference }}</span>
                                        @else
                                            <span class="text-red-600 font-medium">{{ $payout->rejection_reason }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد طلبات سحب أرباح تطابق التصفية.</td>
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
