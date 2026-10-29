@extends('account.layout')

@section('title', 'تذكرة ' . $ticket->ticket_number . ' — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-xs max-w-2xl space-y-6 text-xs">
    <div class="border-b border-stone-100 pb-4">
        <a href="{{ route('account.tickets.index') }}" class="text-stone-500 hover:text-stone-900 font-bold mb-2 inline-block">
            ← العودة لقائمة التذاكر
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-base font-black text-stone-900">{{ $ticket->subject }}</h1>
                <p class="text-stone-500 font-mono text-[11px] mt-0.5">رقم التذكرة: {{ $ticket->ticket_number }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-700">
                    {{ $ticket->priority }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] {{ $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $ticket->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Message Thread -->
    <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
        @foreach($ticket->messages as $msg)
            @if(! $msg->is_internal_note)
                @php
                    $isCustomer = $msg->sender_id === auth()->id();
                @endphp
                <div class="flex flex-col {{ $isCustomer ? 'items-end' : 'items-start' }}">
                    <div class="flex items-center gap-2 mb-1 text-[11px]">
                        <span class="font-bold {{ $isCustomer ? 'text-stone-900' : 'text-amber-800' }}">
                            {{ $isCustomer ? 'أنت' : 'فريق دعم جوردن مارت' }}
                        </span>
                        <span class="text-stone-400">{{ $msg->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="p-4 rounded-2xl max-w-lg text-xs leading-relaxed {{ $isCustomer ? 'bg-stone-900 text-white rounded-te-none' : 'bg-stone-100 text-stone-800 rounded-ts-none' }}">
                        {{ $msg->message }}
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Reply Form (if open) -->
    @if($ticket->status !== 'closed')
        <div class="pt-4 border-t border-stone-100">
            <form method="POST" action="{{ route('account.tickets.reply', $ticket->id) }}" class="space-y-3">
                @csrf
                <label class="block font-bold text-stone-800">إضافة رد:</label>
                <textarea name="message" rows="3" required placeholder="اكتب ردك هنا..." class="w-full rounded-xl border border-stone-300 p-3 focus:border-stone-900"></textarea>
                <div class="flex justify-end">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold transition-all shadow-xs">
                        إرسال الرد
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-3 rounded-xl bg-stone-100 text-stone-500 text-center font-medium">
            هذه التذكرة مغلقة ومكتملة. إذا كنت بحاجة لمساعدة أخرى، يمكنك فتح تذكرة جديدة.
        </div>
    @endif
</div>
@endsection
