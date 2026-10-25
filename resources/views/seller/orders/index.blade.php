@extends('layouts.seller')

@section('title', 'إدارة طلبات المتجر — JordanMart')
@section('page_title', 'إدارة طلبات المتجر')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">طلبات المتجر والشحن</h2>
            <p class="text-xs text-stone-500 mt-0.5">متابعة الطلبات وتحديث حالتها من التجهيز حتى الشحن والتسليم لزبائن الأردن.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('seller.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الطلب (مثال: SO-1234)..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    <option value="">كافة الحالات</option>
                    <option value="pending_confirmation" {{ request('status') === 'pending_confirmation' ? 'selected' : '' }}>بانتظار التأكيد</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد التجهيز</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('seller.orders.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
                        إعادة ضبط
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">الزبون</th>
                        <th class="py-3 px-4 font-bold text-start">المبلغ الإجمالي</th>
                        <th class="py-3 px-4 font-bold text-start">عمولة السوق</th>
                        <th class="py-3 px-4 font-bold text-start">صافي مستحقاتك</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-start">تاريخ الطلب</th>
                        <th class="py-3 px-4 font-bold text-center">تفاصيل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-bold text-stone-900 font-mono">#{{ $order->order_number }}</td>
                            <td class="py-3 px-4 font-medium">{{ $order->parentOrder?->user?->name ?? 'زبون جوردن مارت' }}</td>
                            <td class="py-3 px-4 font-semibold">{{ number_format($order->total_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4 text-stone-500 font-medium">-{{ number_format($order->commission_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4 font-bold text-emerald-600">{{ number_format($order->seller_net_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold
                                    @if($order->status === 'delivered') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($order->status === 'shipped') bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif(in_array($order->status, ['processing', 'confirmed'])) bg-amber-50 text-amber-700 border border-amber-200
                                    @elseif($order->status === 'cancelled') bg-red-50 text-red-700 border border-red-200
                                    @else bg-stone-100 text-stone-700 border border-stone-200 @endif">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="px-2.5 py-1 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold rounded-lg text-xs transition-colors">
                                    معالجة الطلب
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400">لا توجد طلبات مطابقة لمعايير البحث.</td>
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
