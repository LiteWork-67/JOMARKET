@extends('layouts.seller')

@section('title', 'تفاصيل الطلب #' . $order->order_number . ' — JordanMart')
@section('page_title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-black text-stone-900 font-mono">الطلب #{{ $order->order_number }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold
                    @if($order->status === 'delivered') bg-emerald-50 text-emerald-700 border border-emerald-200
                    @elseif($order->status === 'shipped') bg-blue-50 text-blue-700 border border-blue-200
                    @elseif(in_array($order->status, ['processing', 'confirmed'])) bg-amber-50 text-amber-700 border border-amber-200
                    @elseif($order->status === 'cancelled') bg-red-50 text-red-700 border border-red-200
                    @else bg-stone-100 text-stone-700 border border-stone-200 @endif">
                    {{ $order->status }}
                </span>
            </div>
            <p class="text-xs text-stone-500 mt-1">تاريخ الإنشاء: {{ $order->created_at->format('Y-m-d H:i') }} • الطلب الرئيسي: #{{ $order->parentOrder?->order_number }}</p>
        </div>
        <a href="{{ route('seller.orders.index') }}" class="text-xs font-bold text-stone-600 hover:text-stone-900">
            ← العودة لقائمة الطلبات
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Order Items & Delivery Address -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Items -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-stone-100">
                    <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">المنتجات المطلوبة من متجرك</h3>
                </div>
                <div class="divide-y divide-stone-100">
                    @foreach($order->items as $item)
                        <div class="p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @if($item->product?->main_image_url)
                                    <img src="{{ $item->product->main_image_url }}" alt="" class="w-12 h-12 rounded-lg object-cover border border-stone-200 shrink-0">
                                @endif
                                <div>
                                    <p class="text-xs font-bold text-stone-900">{{ $item->product?->name }}</p>
                                    <p class="text-[11px] text-stone-500">رمز المخزون: {{ $item->seller_sku }} @if($item->variant) • {{ $item->variant->title }} @endif</p>
                                    <p class="text-[11px] text-stone-600 mt-0.5">الكمية: <span class="font-bold text-stone-900">{{ $item->quantity }}</span> × {{ number_format($item->unit_price, 3) }} د.أ</p>
                                </div>
                            </div>
                            <div class="text-end shrink-0">
                                <span class="text-xs font-black text-stone-900">{{ number_format($item->total_price, 3) }} د.أ</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider mb-3">عنوان التوصيل (الأردن)</h3>
                @php
                    $address = $order->parentOrder?->shipping_address_snapshot;
                @endphp
                @if($address)
                    <div class="text-xs text-stone-700 space-y-1 leading-relaxed">
                        <p class="font-bold text-stone-900">{{ $address['contact_name'] ?? 'الزبون' }} — <span dir="ltr">{{ $address['phone'] ?? '' }}</span></p>
                        <p>📍 {{ $address['governorate'] ?? '' }}، {{ $address['city'] ?? '' }} @if(!empty($address['district'])) - حي {{ $address['district'] }} @endif</p>
                        <p>{{ $address['street_address'] ?? '' }} @if(!empty($address['building_number'])) ، بناية رقم {{ $address['building_number'] }} @endif @if(!empty($address['floor'])) ، طابق {{ $address['floor'] }} @endif</p>
                        @if(!empty($address['landmark']))
                            <p class="text-stone-500">معلم بارز: {{ $address['landmark'] }}</p>
                        @endif
                        @if(!empty($address['delivery_instructions']))
                            <p class="text-amber-700 font-semibold bg-amber-50 p-2 rounded-lg mt-2">ملاحظات التوصيل: {{ $address['delivery_instructions'] }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-xs text-stone-400">بيانات العنوان غير متوفرة لهذا الطلب.</p>
                @endif
            </div>

            <!-- Status Timeline -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider mb-3">سجل حالات الطلب</h3>
                <div class="space-y-3">
                    @forelse($order->statusHistories as $history)
                        <div class="flex items-start gap-3 text-xs">
                            <div class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-stone-900">{{ $history->status_to }}</span>
                                    <span class="text-[10px] text-stone-400">{{ $history->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                <p class="text-stone-500 text-[11px]">بواسطة: {{ $history->changedByUser?->name ?? 'النظام' }}</p>
                                @if($history->notes)
                                    <p class="text-stone-700 mt-0.5 bg-stone-50 p-2 rounded-lg text-[11px]">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-stone-400">لا توجد سجلات تاريخية بعد.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Col: Financials & Status Update Form -->
        <div class="space-y-6">

            <!-- Financial Summary -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5 space-y-3 text-xs">
                <h3 class="font-black text-stone-900 uppercase tracking-wider">الحساب المالي للطلب</h3>
                
                <div class="flex items-center justify-between py-1 text-stone-600">
                    <span>مجموع المنتجات:</span>
                    <span class="font-semibold text-stone-900">{{ number_format($order->subtotal, 3) }} د.أ</span>
                </div>

                <div class="flex items-center justify-between py-1 text-stone-600">
                    <span>أجور الشحن المخصصة:</span>
                    <span class="font-semibold text-stone-900">{{ number_format($order->shipping_fee, 3) }} د.أ</span>
                </div>

                <div class="flex items-center justify-between py-1 text-stone-600 border-b border-stone-100 pb-2">
                    <span>عمولة المنصة ({{ $order->commission_rate }}%):</span>
                    <span class="font-semibold text-red-600">-{{ number_format($order->commission_amount, 3) }} د.أ</span>
                </div>

                <div class="flex items-center justify-between pt-1 text-sm font-black text-stone-900">
                    <span>صافي مستحقاتك:</span>
                    <span class="text-emerald-600">{{ number_format($order->seller_net_amount, 3) }} د.أ</span>
                </div>
                <p class="text-[10px] text-stone-400">تضاف المستحقات تلقائياً إلى رصيدك المسوى فور تحديث حالة الطلب إلى "تم التسليم (delivered)".</p>
            </div>

            <!-- Status Transition Form -->
            <div class="bg-white rounded-2xl border border-stone-200/80 shadow-xs p-5 space-y-4">
                <h3 class="text-xs font-black text-stone-900 uppercase tracking-wider">تحديث مرحلة الطلب</h3>

                <form method="POST" action="{{ route('seller.orders.update-status', $order->id) }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">الحالة الجديدة <span class="text-red-600">*</span></label>
                        <select name="status" required class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>مؤكد (confirmed)</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>قيد التجهيز والتغليف (processing)</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>تم تسليمه لشركة الشحن (shipped)</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>تم التسليم للزبون بنجاح (delivered)</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>إلغاء الطلب (cancelled)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">شركة التوصيل / السائق</label>
                        <input type="text" name="courier_name" value="{{ old('courier_name', $order->courier_name) }}" placeholder="مثال: أرامكس، مندوب المتجر" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">رقم التتبع (Tracking Number)</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="مطلوب عند الشحن" class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-700 mb-1">ملاحظات التحديث</label>
                        <textarea name="seller_notes" rows="2" placeholder="ملاحظات موجهة لإدارة المنصة أو السجل..." class="w-full text-xs rounded-xl border-stone-200 px-3 py-2 bg-stone-50 focus:bg-white focus:border-amber-500 focus:ring-amber-500">{{ old('seller_notes', $order->seller_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold text-xs rounded-xl shadow-xs transition-colors">
                        تحديث حالة الطلب
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
