@extends('layouts.admin')

@section('title', 'كافة طلبات السوق — JordanMart')
@section('page_title', 'كافة طلبات السوق')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-black text-stone-900">سجل طلبات المتسوقين</h2>
            <p class="text-xs text-stone-500 mt-0.5">متابعة كافة الطلبات الصادرة من الزبائن، تفكيكها حسب البائعين ومراقبة حركة الدفع والتسليم.</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-stone-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الطلب (مثال: ORD-12345)..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-red-500 focus:ring-red-500">
                    <option value="">كافة الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار (Pending)</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكد (Confirmed)</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد المعالجة (Processing)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل (Completed)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي (Cancelled)</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs rounded-xl transition-colors">
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
                        <th class="py-3 px-4 font-bold text-start">المتاجر المشاركة</th>
                        <th class="py-3 px-4 font-bold text-start">إجمالي الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">حالة الدفع</th>
                        <th class="py-3 px-4 font-bold text-start">طريقة الدفع</th>
                        <th class="py-3 px-4 font-bold text-start">التاريخ</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">#{{ $order->order_number }}</td>
                            <td class="py-3 px-4 font-medium">{{ $order->user?->name ?? 'مشتري' }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach($order->sellerOrders as $so)
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] bg-stone-100 text-stone-700">
                                            {{ $so->seller?->store_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 font-black text-stone-900">{{ number_format($order->total_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $order->payment_status ?? 'pending' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-600">{{ $order->payment_method ?? 'COD' }}</td>
                            <td class="py-3 px-4 text-stone-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                    معاينة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400">لا توجد طلبات مسجلة تطابق التصفية.</td>
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
