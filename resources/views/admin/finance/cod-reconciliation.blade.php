@extends('layouts.admin')

@section('title', 'تسوية الدفع عند الاستلام (COD) — الإدارة')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-stone-900">تسوية مبالغ الدفع عند الاستلام (COD Reconciliation)</h1>
            <p class="text-xs text-stone-500 mt-0.5">متابعة تحصيل شركات الشحن والمندوبين لمبالغ الطلبات النقدية وتوريدها للمنصة والمتاجر.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-xs">
            <span class="text-stone-400 text-xs block mb-1">بانتظار التحصيل من العميل</span>
            <span class="text-xl font-black text-amber-600">{{ number_format($stats['total_pending'], 3) }} JOD</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-xs">
            <span class="text-stone-400 text-xs block mb-1">تم التحصيل لدى المندوب</span>
            <span class="text-xl font-black text-blue-600">{{ number_format($stats['total_collected'], 3) }} JOD</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-xs">
            <span class="text-stone-400 text-xs block mb-1">تم التوريد لحساب المنصة</span>
            <span class="text-xl font-black text-purple-600">{{ number_format($stats['total_remitted'], 3) }} JOD</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-xs">
            <span class="text-stone-400 text-xs block mb-1">تمت التسوية مع التاجر</span>
            <span class="text-xl font-black text-emerald-600">{{ number_format($stats['total_settled'], 3) }} JOD</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs">
        <table class="w-full text-xs text-start">
            <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-bold">
                <tr>
                    <th class="p-3.5 text-start">رقم الشحنة</th>
                    <th class="p-3.5 text-start">المتجر البائع</th>
                    <th class="p-3.5 text-start">شركة الشحن / المندوب</th>
                    <th class="p-3.5 text-start">المبلغ المحصل</th>
                    <th class="p-3.5 text-start">حالة التسوية</th>
                    <th class="p-3.5 text-center">تحديث الحالة</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($reconciliations as $rec)
                    <tr class="hover:bg-stone-50/50">
                        <td class="p-3.5 font-mono font-bold text-stone-900">
                            {{ $rec->sellerOrder->order_number }}
                        </td>
                        <td class="p-3.5 font-medium">
                            {{ app()->getLocale() === 'ar' ? $rec->sellerOrder->seller->store_name_ar : $rec->sellerOrder->seller->store_name_en }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-bold">{{ $rec->courier_name }}</span>
                        </td>
                        <td class="p-3.5 font-mono font-black text-emerald-700">
                            {{ number_format($rec->amount_collected, 3) }} JOD
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $rec->status === 'settled_to_seller' ? 'bg-emerald-100 text-emerald-800' : ($rec->status === 'remitted_to_platform' ? 'bg-purple-100 text-purple-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $rec->status }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center">
                            <form method="POST" action="{{ route('admin.finance.cod.update', $rec->id) }}" class="inline-flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="rounded-lg border border-stone-300 p-1 text-[11px]">
                                    <option value="pending_collection" {{ $rec->status === 'pending_collection' ? 'selected' : '' }}>بانتظار التحصيل</option>
                                    <option value="collected_by_courier" {{ $rec->status === 'collected_by_courier' ? 'selected' : '' }}>محصل لدى المندوب</option>
                                    <option value="remitted_to_platform" {{ $rec->status === 'remitted_to_platform' ? 'selected' : '' }}>تم التوريد للمنصة</option>
                                    <option value="settled_to_seller" {{ $rec->status === 'settled_to_seller' ? 'selected' : '' }}>تمت التسوية للتاجر</option>
                                </select>
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-stone-900 text-white font-bold text-[10px]">
                                    حفظ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-stone-400">لا توجد سجلات تسوية نقدية حالياً.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $reconciliations->links() }}
    </div>
</div>
@endsection
