@extends('account.layout')

@section('title', 'طلبات الإرجاع والاستبدال — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-6">
    <div class="flex items-center justify-between border-b border-stone-100 pb-4">
        <div>
            <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
                <span>🔄</span>
                <span>طلبات الإرجاع والاستبدال</span>
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">تتبع وإدارة طلبات استرجاع المنتجات أو استبدالها وفق سياسة حماية المشتري.</p>
        </div>
    </div>

    @if($returnRequests->isEmpty())
        <div class="text-center py-12 text-xs text-stone-500 max-w-sm mx-auto">
            <span class="text-3xl block mb-2">🔄</span>
            <p class="font-bold text-stone-800 mb-1">لا توجد طلبات إرجاع سابقة</p>
            <p class="mb-4">يمكنك طلب إرجاع أو استبدال أي منتج مستلم من خلال صفحة تفاصيل الطلب.</p>
            <a href="{{ route('account.orders') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold transition-colors">
                عرض طلباتي المستلمة
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($returnRequests as $return)
                <div class="p-5 rounded-2xl border border-stone-200 hover:border-stone-300 transition-all bg-white text-xs space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-100 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-stone-900">طلب إرجاع #{{ $return->id }}</span>
                            <span class="text-stone-400">•</span>
                            <span class="text-stone-500">طلب الشحنة: {{ $return->sellerOrder->order_number }}</span>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] {{ $return->status === 'approved' || $return->status === 'refunded' ? 'bg-emerald-100 text-emerald-800' : ($return->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                            {{ $return->status }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-stone-600">
                        <div>
                            <span class="font-bold text-stone-800">نوع الطلب:</span>
                            <span>{{ $return->resolution_type === 'refund' ? 'استرداد المبلغ' : 'استبدال المنتج' }}</span>
                        </div>
                        <div>
                            <span class="text-stone-400">تاريخ الطلب:</span>
                            <span class="font-medium">{{ $return->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                    </div>

                    <p class="text-stone-700 bg-stone-50 p-3 rounded-xl border border-stone-100">
                        <span class="font-bold text-stone-900 block mb-0.5">سبب الإرجاع:</span>
                        {{ $return->reason }}
                    </p>

                    @if($return->admin_notes)
                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-blue-900">
                            <span class="font-bold block mb-0.5">ملاحظات خدمة العملاء:</span>
                            {{ $return->admin_notes }}
                        </div>
                    @endif

                    <div class="pt-2 flex justify-end">
                        <a href="{{ route('account.returns.show', $return->id) }}" class="px-4 py-2 rounded-xl bg-stone-900 text-white font-bold text-xs hover:bg-stone-800">
                            عرض التفاصيل الكاملة →
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $returnRequests->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
