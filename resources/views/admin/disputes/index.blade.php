@extends('layouts.admin')

@section('title', 'إدارة النزاعات وطلبات الإرجاع — الإدارة')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-stone-900">إدارة النزاعات وطلبات الإرجاع</h1>
            <p class="text-xs text-stone-500 mt-0.5">معالجة طلبات الإرجاع، الاسترداد المالي، وإعادة المنتجات للمخزون.</p>
        </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex gap-2">
        <a href="{{ route('admin.disputes.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ !$currentStatus ? 'bg-stone-900 text-white' : 'bg-white border text-stone-700' }}">الكل</a>
        <a href="{{ route('admin.disputes.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'pending' ? 'bg-amber-500 text-stone-950' : 'bg-white border text-stone-700' }}">بانتظار المراجعة</a>
        <a href="{{ route('admin.disputes.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'approved' ? 'bg-blue-600 text-white' : 'bg-white border text-stone-700' }}">تمت الموافقة</a>
        <a href="{{ route('admin.disputes.index', ['status' => 'item_received']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'item_received' ? 'bg-purple-600 text-white' : 'bg-white border text-stone-700' }}">تم استلام المنتج</a>
        <a href="{{ route('admin.disputes.index', ['status' => 'refunded']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'refunded' ? 'bg-emerald-600 text-white' : 'bg-white border text-stone-700' }}">تم الاسترداد المالي</a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs">
        <table class="w-full text-xs text-start">
            <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-bold">
                <tr>
                    <th class="p-3.5 text-start">رقم الطلب</th>
                    <th class="p-3.5 text-start">العميل</th>
                    <th class="p-3.5 text-start">التاجر</th>
                    <th class="p-3.5 text-start">النوع</th>
                    <th class="p-3.5 text-start">الحالة</th>
                    <th class="p-3.5 text-start">تاريخ الطلب</th>
                    <th class="p-3.5 text-center">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($returnRequests as $r)
                    <tr class="hover:bg-stone-50/50">
                        <td class="p-3.5 font-mono font-bold text-stone-900">#{{ $r->id }} ({{ $r->sellerOrder->order_number }})</td>
                        <td class="p-3.5 font-medium">{{ $r->user->name }}</td>
                        <td class="p-3.5 font-medium text-stone-700">{{ app()->getLocale() === 'ar' ? $r->sellerOrder->seller->store_name_ar : $r->sellerOrder->seller->store_name_en }}</td>
                        <td class="p-3.5 font-bold">{{ $r->resolution_type === 'refund' ? 'استرداد مالي' : 'استبدال' }}</td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $r->status === 'refunded' || $r->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td class="p-3.5 text-stone-400">{{ $r->created_at->format('Y-m-d') }}</td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('admin.disputes.show', $r->id) }}" class="px-3 py-1.5 rounded-lg bg-stone-900 text-white font-bold text-[11px] hover:bg-stone-800">
                                معالجة النزاع
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-stone-400">لا توجد طلبات إرجاع مطابقة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $returnRequests->links() }}
    </div>
</div>
@endsection
