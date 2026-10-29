@extends('layouts.admin')

@section('title', 'تذكرة ' . $ticket->ticket_number . ' — الإدارة')

@section('content')
<div class="space-y-6 max-w-4xl text-xs">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.support.index') }}" class="text-stone-500 hover:text-stone-900 font-bold mb-1 inline-block">
                ← العودة لقائمة تذاكر الدعم
            </a>
            <h1 class="text-lg font-black text-stone-900">{{ $ticket->subject }}</h1>
            <p class="text-stone-400 font-mono text-[11px]">رقم التذكرة: {{ $ticket->ticket_number }}</p>
        </div>

        <!-- Quick Status Update -->
        <form method="POST" action="{{ route('admin.support.status', $ticket->id) }}" class="flex items-center gap-2">
            @csrf
            @method('PATCH')
            <select name="status" class="rounded-xl border border-stone-300 p-2 text-xs font-bold">
                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>مفتوحة (Open)</option>
                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>قيد المتابعة (In Progress)</option>
                <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>بانتظار العميل (Waiting)</option>
                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>تم الحل (Resolved)</option>
                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>مغلقة (Closed)</option>
            </select>
            <button type="submit" class="px-3 py-2 rounded-xl bg-stone-900 text-white font-bold">تحديث الحالة</button>
        </form>
    </div>

    <!-- Ticket Info -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-white p-4 rounded-2xl border border-stone-200">
        <div>
            <span class="text-stone-400 block mb-0.5">صاحب التذكرة:</span>
            <span class="font-bold text-stone-900">{{ $ticket->user->name }}</span>
            <span class="text-stone-500 block text-[11px]">{{ $ticket->user->email }} • {{ $ticket->user->phone }}</span>
        </div>
        <div>
            <span class="text-stone-400 block mb-0.5">درجة الأولوية:</span>
            <span class="font-bold {{ $ticket->priority === 'urgent' ? 'text-red-600' : 'text-stone-800' }}">{{ $ticket->priority }}</span>
        </div>
        <div>
            <span class="text-stone-400 block mb-0.5">الطلب المرتبط:</span>
            @if($ticket->parentOrder)
                <a href="{{ route('admin.orders.show', $ticket->parentOrder->id) }}" class="font-mono font-bold text-red-600 hover:underline">
                    {{ $ticket->parentOrder->order_number }}
                </a>
            @else
                <span class="text-stone-500">غير مرتبط بطلب</span>
            @endif
        </div>
    </div>

    <!-- Messages List -->
    <div class="space-y-4">
        @foreach($ticket->messages as $msg)
            @php
                $isAdmin = $msg->sender->hasRole(['admin', 'support']);
            @endphp
            <div class="p-4 rounded-2xl border {{ $msg->is_internal_note ? 'bg-amber-50 border-amber-200' : ($isAdmin ? 'bg-blue-50 border-blue-200' : 'bg-white border-stone-200') }}">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-stone-900">{{ $msg->sender->name }}</span>
                        @if($msg->is_internal_note)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">ملاحظة داخلية للإدارة فقط</span>
                        @elseif($isAdmin)
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-200 text-blue-900">فريق الدعم</span>
                        @endif
                    </div>
                    <span class="text-stone-400 text-[11px]">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <p class="text-stone-800 leading-relaxed">{{ $msg->message }}</p>
            </div>
        @endforeach
    </div>

    <!-- Admin Reply Form -->
    <div class="bg-white p-6 rounded-2xl border border-stone-200 space-y-4">
        <h3 class="font-black text-stone-900 text-sm">إضافة رد أو ملاحظة داخلية</h3>
        <form method="POST" action="{{ route('admin.support.reply', $ticket->id) }}" class="space-y-4">
            @csrf
            <div>
                <textarea name="message" rows="4" required placeholder="اكتب ردك للعميل، أو ملاحظتك الداخلية..." class="w-full rounded-xl border border-stone-300 p-3"></textarea>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_internal_note" value="1" class="rounded border-stone-300 text-amber-500">
                    <span class="font-bold text-amber-800">تعيين كملاحظة داخلية (لن يراها العميل)</span>
                </label>

                <div class="flex items-center gap-3">
                    <select name="status" class="rounded-xl border border-stone-300 p-2 text-xs">
                        <option value="">-- الحفاظ على الحالة --</option>
                        <option value="waiting_customer">بانتظار رد العميل</option>
                        <option value="resolved">تم الحل</option>
                        <option value="closed">إغلاق التذكرة</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold transition-all shadow-xs">
                        إرسال الرد
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
