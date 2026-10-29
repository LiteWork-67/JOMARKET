@extends('layouts.admin')

@section('title', 'تذاكر الدعم وخدمة العملاء — الإدارة')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-stone-900">تذاكر الدعم والشكاوى</h1>
            <p class="text-xs text-stone-500 mt-0.5">متابعة وحل استفسارات العملاء وشكاوى المتاجر والطلبات.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-2">
        <a href="{{ route('admin.support.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ !$currentStatus ? 'bg-stone-900 text-white' : 'bg-white border text-stone-700' }}">الكل</a>
        <a href="{{ route('admin.support.index', ['status' => 'open']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'open' ? 'bg-amber-500 text-stone-950' : 'bg-white border text-stone-700' }}">المفتوحة</a>
        <a href="{{ route('admin.support.index', ['status' => 'in_progress']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-white border text-stone-700' }}">قيد المتابعة</a>
        <a href="{{ route('admin.support.index', ['status' => 'waiting_customer']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'waiting_customer' ? 'bg-purple-600 text-white' : 'bg-white border text-stone-700' }}">بانتظار العميل</a>
        <a href="{{ route('admin.support.index', ['status' => 'resolved']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $currentStatus === 'resolved' ? 'bg-emerald-600 text-white' : 'bg-white border text-stone-700' }}">تم الحل</a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-xs">
        <table class="w-full text-xs text-start">
            <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-bold">
                <tr>
                    <th class="p-3.5 text-start">رقم التذكرة</th>
                    <th class="p-3.5 text-start">العميل</th>
                    <th class="p-3.5 text-start">الموضوع</th>
                    <th class="p-3.5 text-start">الأولوية</th>
                    <th class="p-3.5 text-start">الحالة</th>
                    <th class="p-3.5 text-start">تاريخ الإنشاء</th>
                    <th class="p-3.5 text-center">الإجراء</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($tickets as $t)
                    <tr class="hover:bg-stone-50/50">
                        <td class="p-3.5 font-mono font-bold text-stone-900">{{ $t->ticket_number }}</td>
                        <td class="p-3.5">
                            <span class="font-bold block">{{ $t->user->name }}</span>
                            <span class="text-stone-400 font-mono text-[10px]">{{ $t->user->phone }}</span>
                        </td>
                        <td class="p-3.5 max-w-xs truncate font-medium text-stone-800">{{ $t->subject }}</td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->priority === 'urgent' || $t->priority === 'high' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-700' }}">
                                {{ $t->priority }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $t->status === 'resolved' || $t->status === 'closed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td class="p-3.5 text-stone-400 text-[11px]">{{ $t->created_at->format('Y-m-d H:i') }}</td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('admin.support.show', $t->id) }}" class="px-3 py-1.5 rounded-lg bg-stone-900 text-white font-bold text-[11px] hover:bg-stone-800">
                                فتح التذكرة
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-stone-400">لا توجد تذاكر دعم مطابقة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $tickets->links() }}
    </div>
</div>
@endsection
