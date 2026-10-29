@extends('account.layout')

@section('title', 'تذاكر الدعم الفني والمساعدة — JordanMart')

@section('account_content')
<div class="bg-white rounded-2xl border border-stone-200 p-6 shadow-xs space-y-6">
    <div class="flex items-center justify-between border-b border-stone-100 pb-4">
        <div>
            <h2 class="text-sm font-black text-stone-900 flex items-center gap-2">
                <span>💬</span>
                <span>تذاكر الدعم وخدمة العملاء</span>
            </h2>
            <p class="text-xs text-stone-500 mt-0.5">تواصل مباشرة مع فريق الدعم الفني لحل أي استفسار أو مشكلة في الطلبات.</p>
        </div>

        <a href="{{ route('account.tickets.create') }}" class="px-4 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs transition-colors">
            + تذكرة جديدة
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="text-center py-12 text-xs text-stone-500 max-w-sm mx-auto">
            <span class="text-3xl block mb-2">💬</span>
            <p class="font-bold text-stone-800 mb-1">لا توجد لديك تذاكر دعم حالية</p>
            <p class="mb-4">فريق دعم جوردن مارت جاهز دائماً لمساعدتك في أي وقت.</p>
            <a href="{{ route('account.tickets.create') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-stone-950 font-bold transition-colors">
                فتح تذكرة دعم الآن
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($tickets as $ticket)
                <div class="p-5 rounded-2xl border border-stone-200 hover:border-stone-300 transition-all bg-white text-xs space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-stone-100 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-stone-900">{{ $ticket->ticket_number }}</span>
                            <span class="text-stone-400">•</span>
                            <span class="font-bold text-stone-800">{{ $ticket->subject }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $ticket->priority === 'urgent' || $ticket->priority === 'high' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-700' }}">
                                {{ $ticket->priority }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px] {{ $ticket->status === 'resolved' || $ticket->status === 'closed' ? 'bg-emerald-100 text-emerald-800' : ($ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $ticket->status }}
                            </span>
                        </div>
                    </div>

                    @if($ticket->parentOrder)
                        <p class="text-stone-500 text-[11px]">مرتبطة بالطلب رقم: <span class="font-mono font-bold text-stone-700">{{ $ticket->parentOrder->order_number }}</span></p>
                    @endif

                    <div class="flex items-center justify-between pt-2">
                        <span class="text-stone-400 text-[11px]">آخر تحديث: {{ $ticket->updated_at->diffForHumans() }}</span>
                        <a href="{{ route('account.tickets.show', $ticket->id) }}" class="px-4 py-2 rounded-xl bg-stone-900 text-white font-bold text-xs hover:bg-stone-800">
                            عرض المحادثة والردود →
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="pt-4">
                {{ $tickets->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
