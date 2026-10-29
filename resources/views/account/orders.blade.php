@extends('account.layout')

@section('title', __('messages.my_orders') . ' — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-6">
    <div class="border-b border-stone-100 pb-4">
        <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
            <span>📦</span>
            <span>{{ __('messages.my_orders') }}</span>
        </h2>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-12 text-xs text-stone-500 max-w-sm mx-auto">
            <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-stone-400">
                📦
            </div>
            <p class="font-bold text-stone-800 mb-1">لا توجد لديك طلبات سابقة</p>
            <p class="mb-4">تصفح أقسام جوردن مارت وابدأ تجربة تسوق ممتعة ومضمونة.</p>
            <a href="{{ route('storefront.catalog') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold transition-colors">
                {{ __('messages.start_shopping') }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="p-5 rounded-2xl border border-stone-200 hover:border-stone-300 transition-all bg-white shadow-2xs space-y-4">
                    <!-- Order Top Row -->
                    <div class="flex flex-wrap items-center justify-between gap-3 text-xs border-b border-stone-100 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-black text-stone-900 text-sm">{{ $order->order_number }}</span>
                            <span class="text-stone-400">•</span>
                            <span class="text-stone-500">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>

                    <!-- Shipments Summary -->
                    <div class="space-y-3">
                        @foreach($order->sellerOrders as $sellerOrder)
                            <div class="flex items-center justify-between text-xs bg-stone-50 p-3 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-stone-800">
                                        🏬 {{ app()->getLocale() === 'ar' ? $sellerOrder->seller->store_name_ar : $sellerOrder->seller->store_name_en }}
                                    </span>
                                    <span class="text-[10px] text-stone-400">({{ $sellerOrder->items->count() }} منتجات)</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-white border border-stone-200 text-stone-700">
                                    {{ $sellerOrder->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Bottom Row: Price & Details Action -->
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <span class="text-[11px] text-stone-500 block">{{ __('messages.total') }}</span>
                            <span class="text-base font-black text-stone-950">
                                {{ number_format($order->total_amount, 3) }} {{ __('messages.currency') }}
                            </span>
                        </div>

                        <a href="{{ route('account.orders.show', $order->order_number) }}" class="px-4 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs transition-colors">
                            {{ __('messages.view_order_details') }} →
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
