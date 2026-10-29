@extends('account.layout')

@section('title', __('messages.account_overview') . ' — JordanMart')

@section('account_content')
<div class="space-y-6">
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-xs">
            <span class="text-stone-400 text-xs font-semibold block mb-1">إجمالي الطلبات</span>
            <span class="text-2xl font-black text-stone-900">{{ $stats['total_orders'] }}</span>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-xs">
            <span class="text-stone-400 text-xs font-semibold block mb-1">طلبات جارية</span>
            <span class="text-2xl font-black text-amber-600">{{ $stats['pending_orders'] }}</span>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-xs">
            <span class="text-stone-400 text-xs font-semibold block mb-1">العناوين المحفوظة</span>
            <span class="text-2xl font-black text-stone-900">{{ $stats['addresses_count'] }}</span>
        </div>
        <div class="bg-white rounded-2xl border border-stone-200 p-4 shadow-xs">
            <span class="text-stone-400 text-xs font-semibold block mb-1">قائمة المفضلة</span>
            <span class="text-2xl font-black text-red-600">{{ $stats['wishlist_count'] }}</span>
        </div>
    </div>

    <!-- Default Address Summary -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-stone-100">
            <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
                <span>📍</span>
                <span>{{ __('messages.is_default_address') }}</span>
            </h2>
            <a href="{{ route('account.addresses') }}" class="text-xs font-bold text-red-600 hover:text-red-700">
                {{ __('messages.manage_addresses') }} →
            </a>
        </div>

        @if($defaultAddress)
            <div class="text-xs space-y-1">
                <p class="font-bold text-stone-900">{{ $defaultAddress->contact_name }} ({{ $defaultAddress->phone }})</p>
                <p class="text-stone-600">{{ $defaultAddress->formatted_address }}</p>
                @if($defaultAddress->delivery_instructions)
                    <p class="text-[11px] text-stone-400 italic">ملاحظات: {{ $defaultAddress->delivery_instructions }}</p>
                @endif
            </div>
        @else
            <div class="text-center py-4 text-xs text-stone-500">
                <p class="mb-3">لم تقم بإضافة عنوان توصيل حتى الآن.</p>
                <a href="{{ route('account.addresses') }}" class="inline-flex px-4 py-2 rounded-xl bg-amber-500 text-stone-950 font-bold">
                    + {{ __('messages.add_new_address') }}
                </a>
            </div>
        @endif
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-stone-100 pb-3">
            <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
                <span>📦</span>
                <span>{{ __('messages.recent_orders') }}</span>
            </h2>
            <a href="{{ route('account.orders') }}" class="text-xs font-bold text-red-600 hover:text-red-700">
                عرض كل الطلبات →
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <div class="text-center py-8 text-xs text-stone-500">
                <p class="mb-3">لم تقم بإجراء أي طلبات حتى الآن.</p>
                <a href="{{ route('storefront.catalog') }}" class="inline-flex px-4 py-2 rounded-xl bg-amber-500 text-stone-950 font-bold">
                    {{ __('messages.start_shopping') }}
                </a>
            </div>
        @else
            <div class="divide-y divide-stone-100">
                @foreach($recentOrders as $order)
                    <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <a href="{{ route('account.orders.show', $order->order_number) }}" class="font-mono font-black text-stone-900 hover:text-red-600">
                                    {{ $order->order_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                            <p class="text-stone-500 text-[11px]">
                                {{ $order->created_at->format('Y-m-d') }} • 
                                {{ $order->sellerOrders->count() }} شحنات • 
                                {{ $order->payment_method === 'bank_transfer' ? 'كليك (CliQ)' : 'عند الاستلام' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="font-black text-stone-950 text-sm">
                                {{ number_format($order->total_amount, 3) }} {{ __('messages.currency') }}
                            </span>
                            <a href="{{ route('account.orders.show', $order->order_number) }}" class="px-3 py-1.5 rounded-xl border border-stone-200 hover:bg-stone-50 font-bold text-stone-700 transition-colors">
                                {{ __('messages.view_order_details') }}
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
