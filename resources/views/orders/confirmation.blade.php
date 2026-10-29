@extends('layouts.storefront')

@section('title', __('messages.order_confirmed_title') . ' — JordanMart')

@section('content')
<div class="py-12 bg-stone-50 min-h-[80vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Confirmation Hero Card -->
        <div class="bg-white rounded-3xl border border-stone-200 p-8 sm:p-10 text-center shadow-xs mb-8">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-xs ring-8 ring-emerald-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight mb-2">
                {{ __('messages.order_confirmed_title') }}
            </h1>
            <p class="text-xs sm:text-sm text-stone-500 max-w-lg mx-auto leading-relaxed mb-6">
                {{ __('messages.order_confirmed_subtitle') }}
            </p>

            <div class="inline-flex flex-wrap items-center justify-center gap-4 p-4 rounded-2xl bg-stone-50 border border-stone-200 text-xs">
                <div>
                    <span class="text-stone-500 block text-[10px]">{{ __('messages.order_number') }}</span>
                    <span class="font-mono font-black text-stone-900 text-sm">{{ $order->order_number }}</span>
                </div>
                <div class="h-6 w-px bg-stone-200 hidden sm:block"></div>
                <div>
                    <span class="text-stone-500 block text-[10px]">{{ __('messages.order_date') }}</span>
                    <span class="font-bold text-stone-800">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="h-6 w-px bg-stone-200 hidden sm:block"></div>
                <div>
                    <span class="text-stone-500 block text-[10px]">{{ __('messages.total') }}</span>
                    <span class="font-black text-emerald-700 text-sm">{{ number_format($order->total_amount, 3) }} {{ __('messages.currency') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Instructions Card (COD or CliQ) -->
        <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs mb-6">
            <h2 class="text-xs font-black text-stone-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span>💳 {{ __('messages.payment_method') }}:</span>
                <span class="text-stone-700 font-bold">
                    {{ $order->payment_method === 'bank_transfer' ? __('messages.payment_cliq') : __('messages.payment_cod') }}
                </span>
            </h2>

            @if($order->payment_method === 'bank_transfer')
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 space-y-2">
                    <p class="font-bold">يرجى إتمام التحويل عبر تطبيق بنكك باستخدام خدمة كليك (CliQ):</p>
                    <div class="flex flex-wrap gap-4 items-center bg-white p-3 rounded-lg border border-emerald-200 font-mono text-xs">
                        <div>
                            <span class="text-[10px] text-stone-400 block">معرف كليك (CliQ Alias)</span>
                            <span class="font-black text-stone-900 text-sm">JORDANMART</span>
                        </div>
                        <div class="h-6 w-px bg-stone-200"></div>
                        <div>
                            <span class="text-[10px] text-stone-400 block">المبلغ المطلوب بدقة</span>
                            <span class="font-black text-emerald-700 text-sm">{{ number_format($order->total_amount, 3) }} JOD</span>
                        </div>
                        <div class="h-6 w-px bg-stone-200"></div>
                        <div>
                            <span class="text-[10px] text-stone-400 block">رمز المرجع / الوصف</span>
                            <span class="font-black text-stone-900">{{ $order->order_number }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-200 text-xs text-stone-800 flex items-start gap-3">
                    <span class="text-xl">💵</span>
                    <div>
                        <p class="font-bold mb-0.5">الدفع عند الاستلام نقداً</p>
                        <p class="text-stone-600">يرجى تحضير المبلغ المطلوب وقدره <span class="font-black text-stone-900">{{ number_format($order->total_amount, 3) }} {{ __('messages.currency') }}</span> نقداً لتسليمه لمندوب شركة الشحن عند الاستلام.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Shipments & Items Breakdown -->
        <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs mb-8 space-y-6">
            <h2 class="text-sm font-black text-stone-900 border-b border-stone-100 pb-3">
                تفاصيل الشحنات والمتاجر ({{ $order->sellerOrders->count() }} شحنات)
            </h2>

            @foreach($order->sellerOrders as $sellerOrder)
                <div class="p-4 rounded-xl border border-stone-200 bg-stone-50/50 space-y-3">
                    <div class="flex items-center justify-between text-xs border-b border-stone-200 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-stone-900">
                                🏬 {{ app()->getLocale() === 'ar' ? $sellerOrder->seller->store_name_ar : $sellerOrder->seller->store_name_en }}
                            </span>
                            <span class="text-[10px] text-stone-500 font-mono">({{ $sellerOrder->order_number }})</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">
                            {{ $sellerOrder->status }}
                        </span>
                    </div>

                    <div class="space-y-2">
                        @foreach($sellerOrder->items as $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="w-5 text-center font-bold text-stone-500">{{ $item->quantity }}x</span>
                                    <span class="font-semibold text-stone-800">
                                        {{ app()->getLocale() === 'ar' ? $item->product_title_ar : $item->product_title_en }}
                                    </span>
                                </div>
                                <span class="font-bold text-stone-900">
                                    {{ number_format((float) $item->unit_price * $item->quantity, 3) }} {{ __('messages.currency') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Shipping Address Snapshot -->
            <div class="pt-4 border-t border-stone-100 text-xs text-stone-600">
                <span class="font-bold text-stone-900 block mb-1">📍 عنوان التوصيل المحدد:</span>
                <p>
                    {{ $order->shipping_address_snapshot['contact_name'] ?? '' }} — 
                    {{ $order->shipping_address_snapshot['phone'] ?? '' }}
                </p>
                <p class="text-stone-500 mt-0.5">
                    {{ $order->shipping_address_snapshot['governorate'] ?? '' }}، 
                    {{ $order->shipping_address_snapshot['city'] ?? '' }}، 
                    {{ $order->shipping_address_snapshot['street_address'] ?? '' }}
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('account.orders') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs text-center transition-colors shadow-xs">
                {{ __('messages.my_orders') }}
            </a>
            <a href="{{ route('storefront.home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700 font-bold text-xs text-center transition-colors">
                {{ __('messages.continue_shopping') }}
            </a>
        </div>

    </div>
</div>
@endsection
