@extends('account.layout')

@section('title', 'تفاصيل الطلب ' . $order->order_number . ' — JordanMart')

@section('account_content')
<div class="space-y-6">
    
    <!-- Top Action & Overview -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-stone-100 pb-4 mb-4">
            <div>
                <a href="{{ route('account.orders') }}" class="text-xs text-stone-500 hover:text-stone-900 font-bold mb-2 inline-block">
                    ← العودة لقائمة الطلبات
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-black text-stone-900 font-mono">{{ $order->order_number }}</h1>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                        {{ $order->status }}
                    </span>
                </div>
                <p class="text-[11px] text-stone-500 mt-1">تاريخ الإنشاء: {{ $order->created_at->format('Y-m-d H:i') }}</p>
            </div>

            <!-- Cancel Button if Pending -->
            @if($order->status === 'pending')
                <div x-data="{ open: false }">
                    <button type="button" @click="open = true" class="px-4 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold text-xs transition-colors border border-red-200">
                        {{ __('messages.cancel_order') }}
                    </button>

                    <!-- Cancel Modal -->
                    <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-xs">
                        <div class="bg-white rounded-2xl max-w-md w-full p-6 text-xs text-start shadow-xl border border-stone-200" @click.outside="open = false">
                            <h3 class="text-sm font-black text-stone-900 mb-2">{{ __('messages.cancel_order_confirm') }}</h3>
                            <p class="text-stone-500 mb-4 leading-relaxed">سيتم إلغاء جميع شحنات الطلب وإعادة المنتجات المحجوزة لمخزون المتاجر فوراً.</p>
                            <form method="POST" action="{{ route('account.orders.cancel', $order->order_number) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block font-bold text-stone-700 mb-1">{{ __('messages.cancel_order_reason') }}</label>
                                    <select name="reason" class="w-full rounded-xl border border-stone-300 p-2.5">
                                        <option value="Changed mind">غيرت رأيي</option>
                                        <option value="Ordered by mistake">تم الطلب بالخطأ</option>
                                        <option value="Found better price">وجدت سعراً أفضل</option>
                                        <option value="Delivery time too long">مدة التوصيل طويلة</option>
                                        <option value="Other">سبب آخر</option>
                                    </select>
                                </div>
                                <div class="flex items-center justify-end gap-3 pt-2">
                                    <button type="button" @click="open = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-700 font-bold">تراجع</button>
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold">تأكيد الإلغاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Meta info -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3 bg-stone-50 rounded-xl">
                <span class="text-stone-400 block mb-1">طريقة الدفع:</span>
                <span class="font-bold text-stone-800">
                    {{ $order->payment_method === 'bank_transfer' ? 'كليك (CliQ)' : 'الدفع عند الاستلام' }}
                </span>
                <span class="text-[10px] text-stone-500 block">حالة الدفع: {{ $order->payment_status }}</span>
            </div>

            <div class="p-3 bg-stone-50 rounded-xl sm:col-span-2">
                <span class="text-stone-400 block mb-1">عنوان التوصيل:</span>
                <span class="font-bold text-stone-800">{{ $order->shipping_address_snapshot['contact_name'] ?? '' }} ({{ $order->shipping_address_snapshot['phone'] ?? '' }})</span>
                <p class="text-stone-600 text-[11px] mt-0.5">
                    {{ $order->shipping_address_snapshot['governorate'] ?? '' }}، 
                    {{ $order->shipping_address_snapshot['city'] ?? '' }}، 
                    {{ $order->shipping_address_snapshot['street_address'] ?? '' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Seller Shipments & Items -->
    <div class="space-y-4">
        @foreach($order->sellerOrders as $sellerOrder)
            <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-stone-900 text-xs">
                            🏬 متجر: {{ app()->getLocale() === 'ar' ? $sellerOrder->seller->store_name_ar : $sellerOrder->seller->store_name_en }}
                        </span>
                        <span class="text-[10px] text-stone-400 font-mono">({{ $sellerOrder->order_number }})</span>
                    </div>

                    <div class="flex items-center gap-2">
                        @if(in_array($sellerOrder->status, ['delivered', 'completed']))
                            <a href="{{ route('account.returns.create', $sellerOrder->id) }}" class="px-2.5 py-1 rounded-lg bg-stone-100 hover:bg-stone-200 text-stone-700 text-[10px] font-bold">
                                🔄 طلب إرجاع
                            </a>
                        @endif
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-800">
                            {{ $sellerOrder->status }}
                        </span>
                        @if($sellerOrder->tracking_number)
                            <span class="text-[10px] text-stone-500 font-mono">تتبع: {{ $sellerOrder->tracking_number }}</span>
                        @endif
                    </div>
                </div>

                <!-- Items -->
                <div class="divide-y divide-stone-100">
                    @foreach($sellerOrder->items as $item)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-12 h-12 rounded-xl bg-stone-100 border border-stone-200 shrink-0 overflow-hidden flex items-center justify-center">
                                    @if($item->product && $item->product->primary_image_url)
                                        <img src="{{ $item->product->primary_image_url }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[10px] font-black text-stone-400">JM</span>
                                    @endif
                                </div>
                                <div class="truncate">
                                    <a href="{{ route('storefront.product', $item->product->slug ?? '#') }}" class="font-bold text-stone-900 hover:text-red-600 truncate block">
                                        {{ app()->getLocale() === 'ar' ? $item->product_title_ar : $item->product_title_en }}
                                    </a>
                                    <span class="text-[10px] text-stone-500">
                                        الكمية: {{ $item->quantity }} × {{ number_format($item->unit_price, 3) }} {{ __('messages.currency') }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-end shrink-0 flex items-center gap-4">
                                <span class="font-black text-stone-900">
                                    {{ number_format((float) $item->unit_price * $item->quantity, 3) }} {{ __('messages.currency') }}
                                </span>

                                @if(in_array($sellerOrder->status, ['delivered', 'completed']))
                                    <button type="button" @click="$dispatch('open-review-modal', { productId: {{ $item->product_id }}, orderItemId: {{ $item->id }}, productTitle: '{{ addslashes($item->product_title_ar) }}' })" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 border border-amber-200 text-[10px] font-bold hover:bg-amber-100">
                                        ⭐ تقييم
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- Financial Invoice Summary -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-3 text-xs">
        <h3 class="font-black text-stone-900 border-b border-stone-100 pb-2">تفاصيل الفاتورة</h3>
        <div class="flex justify-between text-stone-600">
            <span>{{ __('messages.subtotal') }}</span>
            <span class="font-bold text-stone-900">{{ number_format($order->subtotal_amount, 3) }} {{ __('messages.currency') }}</span>
        </div>
        <div class="flex justify-between text-stone-600">
            <span>{{ __('messages.shipping_fee') }}</span>
            <span class="font-bold text-stone-900">{{ number_format($order->shipping_amount, 3) }} {{ __('messages.currency') }}</span>
        </div>
        @if($order->discount_amount > 0)
            <div class="flex justify-between text-emerald-600 font-semibold">
                <span>{{ __('messages.discount') }}</span>
                <span>-{{ number_format($order->discount_amount, 3) }} {{ __('messages.currency') }}</span>
            </div>
        @endif
        <div class="pt-2 border-t border-stone-200 flex justify-between items-baseline text-sm">
            <span class="font-black text-stone-900">{{ __('messages.total') }}</span>
            <span class="font-black text-stone-950 text-base">{{ number_format($order->total_amount, 3) }} {{ __('messages.currency') }}</span>
        </div>
    </div>

</div>

<!-- Review Modal Component for Delivered Products -->
<div x-data="{ open: false, productId: null, orderItemId: null, productTitle: '', rating: 5 }"
    @open-review-modal.window="open = true; productId = $event.detail.productId; orderItemId = $event.detail.orderItemId; productTitle = $event.detail.productTitle">
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 text-xs text-start shadow-xl border border-stone-200" @click.outside="open = false">
            <h3 class="text-sm font-black text-stone-900 mb-1">تقييم منتج تم شراؤه</h3>
            <p class="text-stone-500 mb-4" x-text="productTitle"></p>

            <form method="POST" action="{{ route('account.reviews.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="productId">
                <input type="hidden" name="order_item_id" :value="orderItemId">

                <div>
                    <label class="block font-bold text-stone-700 mb-1">التقييم بالنجوم (1 إلى 5):</label>
                    <select name="rating" x-model="rating" class="w-full rounded-xl border border-stone-300 p-2.5">
                        <option value="5">⭐⭐⭐⭐⭐ ممتاز (5/5)</option>
                        <option value="4">⭐⭐⭐⭐ جيد جداً (4/5)</option>
                        <option value="3">⭐⭐⭐ جيد (3/5)</option>
                        <option value="2">⭐⭐ مقبول (2/5)</option>
                        <option value="1">⭐ ضعيف (1/5)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">عنوان التقييم (اختياري):</label>
                    <input type="text" name="title" placeholder="مثال: منتج رائع وخامة ممتازة" class="w-full rounded-xl border border-stone-300 p-2.5">
                </div>

                <div>
                    <label class="block font-bold text-stone-700 mb-1">رأيك وتجربتك بالتفصيل *</label>
                    <textarea name="comment" rows="3" required placeholder="شارك المشترين الآخرين تفاصيل تجربتك وجودة المنتج..." class="w-full rounded-xl border border-stone-300 p-2.5"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="open = false" class="px-4 py-2 rounded-xl border border-stone-200 text-stone-700 font-bold">إلغاء</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold">إرسال التقييم</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
