@extends('layouts.seller')

@section('title', 'لوحة تحكم البائع — ' . $seller->store_name)
@section('page_title', 'لوحة تحكم البائع')

@section('content')
<div class="space-y-6">

    <!-- Welcome & Store Status Banner -->
    <div class="bg-gradient-to-r from-stone-900 to-stone-800 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black">{{ $seller->store_name }}</h2>
            <p class="text-xs text-stone-300 mt-1">مرحباً بك، يمكنك إدارة عروضك، متابعة طلبات الزبائن وسحب أرباحك الصافية بأمان.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('seller.offers.create') }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>إضافة عرض جديد</span>
            </a>
            <a href="{{ route('seller.payouts.create') }}" class="px-4 py-2 bg-stone-700 hover:bg-stone-600 text-white font-bold text-xs rounded-xl transition-colors">
                <span>طلب سحب أرباح</span>
            </a>
        </div>
    </div>

    <!-- Real Database Metric Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Settled Balance -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">الرصيد المتاح للسحب</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                {{ number_format($settledBalance, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">صافي المبيعات المسواة</p>
        </div>

        <!-- Total Delivered Sales -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي المبيعات المكتملة</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ number_format($totalSales, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">من {{ $deliveredOrders }} طلب تم تسليمه</p>
        </div>

        <!-- Pending / In-progress Orders -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">طلبات بحاجة للتجهيز</span>
            <div class="text-2xl font-black text-amber-600 mt-1">
                {{ $pendingOrders }}
            </div>
            <p class="text-[11px] text-stone-400 mt-1">{{ $shippedOrders }} طلب قيد الشحن والتوصيل</p>
        </div>

        <!-- Commission Deducted -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">عمولة المنصة المقتطعة</span>
            <div class="text-2xl font-black text-stone-700 mt-1">
                {{ number_format($commissionDeducted, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">بنسبة {{ $seller->commission_rate }}%</p>
        </div>
    </div>

    <!-- Second Row: Inventory & Catalog Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-stone-500">العروض النشطة بالسوق</p>
                <p class="text-2xl font-black text-stone-900 mt-1">{{ $activeOffers }}</p>
            </div>
            <a href="{{ route('seller.offers.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">عرض العروض ←</a>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-stone-500">منتجات أوشكت على النفاد</p>
                <p class="text-2xl font-black {{ $lowStockOffers > 0 ? 'text-red-600' : 'text-stone-900' }} mt-1">{{ $lowStockOffers }}</p>
            </div>
            <a href="{{ route('seller.inventory.index', ['low_stock' => 1]) }}" class="text-xs font-bold text-red-600 hover:text-red-700">تحديث المخزون ←</a>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-stone-500">إجمالي طلبات المتجر</p>
                <p class="text-2xl font-black text-stone-900 mt-1">{{ $totalOrders }}</p>
            </div>
            <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">سجل الطلبات ←</a>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-stone-900">أحدث الطلبات الواردة لمتجرك</h3>
            <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">عرض كافة الطلبات</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم الطلب</th>
                        <th class="py-3 px-4 font-bold text-start">الزبون</th>
                        <th class="py-3 px-4 font-bold text-start">المبلغ الإجمالي</th>
                        <th class="py-3 px-4 font-bold text-start">صافي أرباحك</th>
                        <th class="py-3 px-4 font-bold text-start">الحالة</th>
                        <th class="py-3 px-4 font-bold text-start">التاريخ</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-bold text-stone-900">#{{ $order->order_number }}</td>
                            <td class="py-3 px-4">{{ $order->parentOrder?->user?->name ?? 'زبون جوردن مارت' }}</td>
                            <td class="py-3 px-4 font-semibold">{{ number_format($order->total_amount, 3) }} د.أ</td>
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
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                    معاينة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد طلبات واردة حالياً لهذا المتجر.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
