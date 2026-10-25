@extends('layouts.admin')

@section('title', 'تقرير عمولات المنصة — JordanMart')
@section('page_title', 'تقرير عمولات المنصة والإيرادات')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">إيرادات العمولات من مبيعات التجار المكتملة</h2>
            <p class="text-xs text-stone-500 mt-0.5">حساب العمولات الفعلية المقتطعة من كافة طلبات السوق التي وصلت لحالة التسليم (Delivered).</p>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي المبيعات المكتملة المستهدفة</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ number_format($totalDeliveredSales, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي دخل العمولات الصافي للمنصة</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                {{ number_format($totalCommissions, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.finance.commissions') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <select name="seller_id" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">كافة المتاجر</option>
                    @foreach($sellers as $sel)
                        <option value="{{ $sel->id }}" {{ request('seller_id') == $sel->id ? 'selected' : '' }}>
                            {{ $sel->store_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية التقرير
                </button>
                @if(request()->hasAny(['seller_id', 'from_date', 'to_date']))
                    <a href="{{ route('admin.finance.commissions') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        إعادة ضبط
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Commission Breakdown Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">المتجر / البائع</th>
                        <th class="py-3 px-4 font-bold text-start">مجموع الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">نسبة العمولة</th>
                        <th class="py-3 px-4 font-bold text-start">عمولة المنصة</th>
                        <th class="py-3 px-4 font-bold text-start">صافي التاجر</th>
                        <th class="py-3 px-4 font-bold text-start">تاريخ التسليم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">#{{ $order->order_number }}</td>
                            <td class="py-3 px-4 font-medium">{{ $order->seller?->store_name }}</td>
                            <td class="py-3 px-4 font-semibold text-stone-900">{{ number_format($order->total_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4 font-bold text-stone-600">{{ $order->commission_rate }}%</td>
                            <td class="py-3 px-4 font-black text-emerald-600">+{{ number_format($order->commission_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4 font-semibold text-stone-700">{{ number_format($order->seller_net_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4 text-stone-500">{{ $order->delivered_at?->format('Y-m-d H:i') ?? $order->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد طلبات مسلمة تطابق شروط التصفية.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="p-4 border-t border-stone-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
