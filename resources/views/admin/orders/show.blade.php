@extends('layouts.admin')

@section('title', 'تفاصيل الطلب #' . $order->order_number . ' — JordanMart')
@section('page_title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-stone-900 font-mono">الطلب الرئيسي #{{ $order->order_number }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold
                    @if($order->status === 'completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif($order->status === 'cancelled') bg-red-50 text-red-700 border border-red-200
                    @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                    {{ $order->status }}
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">تاريخ الطلب: {{ $order->created_at->format('Y-m-d H:i') }} • المشتري: {{ $order->user?->name }} ({{ $order->user?->email }})</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة لقائمة الطلبات
        </a>
    </div>

    <!-- Fulfillment Groups (Seller Orders) -->
    <div class="space-y-4">
        <h3 class="text-sm font-black text-stone-900">مجموعات تجهيز الطلب حسب المتاجر (Seller Fulfillment Groups)</h3>

        @foreach($order->sellerOrders as $sellerOrder)
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-stone-100 gap-2">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-xs text-stone-900">{{ $sellerOrder->seller?->store_name }}</span>
                        <span class="font-mono text-xs text-stone-400">#{{ $sellerOrder->order_number }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold
                            @if($sellerOrder->status === 'delivered') bg-emerald-50 text-emerald-700
                            @elseif($sellerOrder->status === 'shipped') bg-blue-50 text-blue-700
                            @else bg-stone-100 text-stone-700 @endif">
                            {{ $sellerOrder->status }}
                        </span>
                        @if($sellerOrder->tracking_number)
                            <span class="text-xs text-stone-500 font-mono">تتبع: {{ $sellerOrder->courier_name }} ({{ $sellerOrder->tracking_number }})</span>
                        @endif
                    </div>
                </div>

                <!-- Items in this seller group -->
                <div class="divide-y divide-stone-100">
                    @foreach($sellerOrder->items as $item)
                        <div class="py-2.5 flex items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-3">
                                @if($item->product?->main_image_url)
                                    <img src="{{ $item->product->main_image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover border border-stone-200 shrink-0">
                                @endif
                                <div>
                                    <p class="font-bold text-stone-900">{{ $item->product?->name }}</p>
                                    <p class="text-[10px] text-stone-400">رمز SKU: {{ $item->seller_sku }}</p>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="font-bold text-stone-900">{{ $item->quantity }} × {{ number_format($item->unit_price, 3) }} د.أ</span>
                                <span class="block text-[11px] font-black text-stone-900">{{ number_format($item->total_price, 3) }} د.أ</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Financial calculation for this group -->
                <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-xs text-stone-600 bg-stone-50 -mx-5 -mb-5 p-4 rounded-b-2xl">
                    <div>
                        <span>المجموع: <strong class="text-stone-900">{{ number_format($sellerOrder->subtotal, 3) }} د.أ</strong></span> • 
                        <span>الشحن: <strong class="text-stone-900">{{ number_format($sellerOrder->shipping_fee, 3) }} د.أ</strong></span>
                    </div>
                    <div>
                        <span>عمولة المنصة ({{ $sellerOrder->commission_rate }}%): <strong class="text-red-600">{{ number_format($sellerOrder->commission_amount, 3) }} د.أ</strong></span> • 
                        <span>صافي البائع: <strong class="text-emerald-700">{{ number_format($sellerOrder->seller_net_amount, 3) }} د.أ</strong></span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Bottom details: Customer Address & Overall Financials -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Shipping & Customer Snapshot -->
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5 text-xs space-y-2">
            <h3 class="font-black text-stone-900 uppercase tracking-wider mb-2">عنوان الشحن والتوصيل</h3>
            @php $addr = $order->shipping_address_snapshot; @endphp
            @if($addr)
                <p class="font-bold text-stone-900">{{ $addr['contact_name'] ?? '—' }} ({{ $addr['phone'] ?? '' }})</p>
                <p class="text-stone-600">المحافظة: {{ $addr['governorate'] ?? '' }} • المدينة: {{ $addr['city'] ?? '' }}</p>
                <p class="text-stone-600">{{ $addr['street_address'] ?? '' }} - بناية {{ $addr['building_number'] ?? '' }}</p>
                @if(!empty($addr['delivery_instructions']))
                    <p class="text-amber-800 bg-amber-50 p-2 rounded-lg mt-2">ملاحظات: {{ $addr['delivery_instructions'] }}</p>
                @endif
            @else
                <p class="text-stone-400">العنوان غير متوفر</p>
            @endif
        </div>

        <!-- Overall Totals -->
        <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5 text-xs space-y-2">
            <h3 class="font-black text-stone-900 uppercase tracking-wider mb-2">ملخص الحساب الإجمالي للطلب</h3>
            <div class="flex items-center justify-between py-1 text-stone-600">
                <span>مجموع المنتجات:</span>
                <span class="font-semibold text-stone-900">{{ number_format($order->subtotal_amount, 3) }} د.أ</span>
            </div>
            <div class="flex items-center justify-between py-1 text-stone-600">
                <span>إجمالي الشحن:</span>
                <span class="font-semibold text-stone-900">{{ number_format($order->shipping_amount, 3) }} د.أ</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex items-center justify-between py-1 text-stone-600">
                    <span>الخصومات والكوبونات:</span>
                    <span class="font-semibold text-emerald-600">-{{ number_format($order->discount_amount, 3) }} د.أ</span>
                </div>
            @endif
            <div class="flex items-center justify-between pt-2 border-t border-stone-100 text-sm font-black text-stone-900">
                <span>المبلغ الكلي المدفوع:</span>
                <span class="text-red-600">{{ number_format($order->total_amount, 3) }} د.أ</span>
            </div>
            <p class="text-[11px] text-stone-400 pt-1">طريقة الدفع: {{ $order->payment_method }} (حالة السداد: {{ $order->payment_status }})</p>
        </div>

    </div>

</div>
@endsection
