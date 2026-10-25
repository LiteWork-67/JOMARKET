@extends('layouts.admin')

@section('title', 'لوحة تحكم إدارة منصة JordanMart')
@section('page_title', 'لوحة المؤشرات والتحليلات المركزية')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards (Real DB queries) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total GMV -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">حجم مبيعات المنصة (GMV)</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ number_format($totalGMV, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">إجمالي مبيعات الطلبات المسلمة</p>
        </div>

        <!-- Total Commissions Earned -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إيرادات العمولات المحصلة</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">
                {{ number_format($totalCommissions, 3) }} <span class="text-xs font-bold text-stone-600">د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">دخل المنصة التشغيلي الصافي</p>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">إجمالي طلبات المشترين</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ $totalOrders }}
            </div>
            <p class="text-[11px] text-stone-400 mt-1">{{ $pendingOrders }} طلب قيد المعالجة</p>
        </div>

        <!-- Active Sellers -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/80 shadow-xs">
            <span class="text-xs font-semibold text-stone-500">التجار المعتمدون</span>
            <div class="text-2xl font-black text-stone-900 mt-1">
                {{ $activeSellers }} <span class="text-xs font-normal text-stone-400">/ {{ $totalSellers }}</span>
            </div>
            <p class="text-[11px] text-stone-400 mt-1">{{ $totalCustomers }} مستخدم متسوق مسجل</p>
        </div>
    </div>

    <!-- Attention Needed Action Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border {{ $pendingSellerApprovals > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-stone-200/80' }} shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-stone-700">طلبات انضمام تجار بانتظار الاعتماد</span>
                <div class="text-xl font-black {{ $pendingSellerApprovals > 0 ? 'text-amber-600' : 'text-stone-900' }} mt-0.5">
                    {{ $pendingSellerApprovals }} طلب بانتظار المراجعة
                </div>
            </div>
            <a href="{{ route('admin.sellers.index', ['status' => 'pending']) }}" class="px-3.5 py-1.5 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                مراجعة التجار ←
            </a>
        </div>

        <div class="bg-white rounded-2xl p-5 border {{ $pendingProducts > 0 ? 'border-amber-300 bg-amber-50/30' : 'border-stone-200/80' }} shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-stone-700">منتجات مقترحة بانتظار الاعتماد</span>
                <div class="text-xl font-black {{ $pendingProducts > 0 ? 'text-amber-600' : 'text-stone-900' }} mt-0.5">
                    {{ $pendingProducts }} منتج بانتظار الموافقة
                </div>
            </div>
            <a href="{{ route('admin.products.index', ['status' => 'pending_approval']) }}" class="px-3.5 py-1.5 bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs rounded-xl transition-colors">
                تدقيق المنتجات ←
            </a>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-stone-900">أحدث طلبات السوق الإجمالية</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700">عرض جميع الطلبات</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-bold text-start">رقم الطلب الرئيسي</th>
                        <th class="py-3 px-4 font-bold text-start">الزبون</th>
                        <th class="py-3 px-4 font-bold text-start">المتاجر المشاركة</th>
                        <th class="py-3 px-4 font-bold text-start">المبلغ الإجمالي</th>
                        <th class="py-3 px-4 font-bold text-start">حالة الدفع</th>
                        <th class="py-3 px-4 font-bold text-start">التاريخ</th>
                        <th class="py-3 px-4 font-bold text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-stone-50/60">
                            <td class="py-3 px-4 font-bold text-stone-900 font-mono">#{{ $order->order_number }}</td>
                            <td class="py-3 px-4">{{ $order->user?->name ?? 'مشتري زائر' }}</td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach($order->sellerOrders as $so)
                                        <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] bg-stone-100 text-stone-700">
                                            {{ $so->seller?->store_name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4 font-bold text-stone-900">{{ number_format($order->total_amount, 3) }} د.أ</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $order->payment_status ?? 'pending' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-lg text-xs transition-colors">
                                    معاينة
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">لا توجد طلبات مسجلة بالنظام حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
