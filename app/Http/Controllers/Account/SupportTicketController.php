<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\ParentOrder;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(): View
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->with(['seller', 'parentOrder'])
            ->latest()
            ->paginate(10);

        return view('account.tickets.index', [
            'tickets' => $tickets,
        ]);
    }

    public function create(Request $request): View
    {
        $userOrders = ParentOrder::where('user_id', Auth::id())
            ->latest()
            ->take(20)
            ->get();

        $selectedOrderId = $request->query('order_id');

        return view('account.tickets.create', [
            'userOrders' => $userOrders,
            'selectedOrderId' => $selectedOrderId,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'parent_order_id' => ['nullable', 'exists:parent_orders,id'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $ticket = DB::transaction(function () use ($validated) {
            $ticketNumber = 'TKT-'.strtoupper(Str::random(8));

            $ticket = SupportTicket::create([
                'ticket_number' => $ticketNumber,
                'user_id' => Auth::id(),
                'parent_order_id' => $validated['parent_order_id'] ?? null,
                'subject' => $validated['subject'],
                'priority' => $validated['priority'],
                'status' => 'open',
            ]);

            SupportTicketMessage::create([
                'support_ticket_id' => $ticket->id,
                'sender_id' => Auth::id(),
                'message' => $validated['message'],
                'is_internal_note' => false,
            ]);

            return $ticket;
        });

        return redirect()->route('account.tickets.show', $ticket->id)
            ->with('success', 'تم فتح تذكرة الدعم بنجاح. سيقوم فريق خدمة العملاء بالرد عليك قريباً.');
    }

    public function show(SupportTicket $supportTicket): View
    {
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403);
        }

        $supportTicket->load(['messages.sender', 'parentOrder', 'seller']);

        return view('account.tickets.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function reply(SupportTicket $supportTicket, Request $request): RedirectResponse
    {
        if ($supportTicket->user_id !== Auth::id()) {
            abort(403);
        }

        if ($supportTicket->status === 'closed') {
            return back()->with('error', 'هذه التذكرة مغلقة ولا يمكن إضافة ردود جديدة.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:5000'],
        ]);

        SupportTicketMessage::create([
            'support_ticket_id' => $supportTicket->id,
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal_note' => false,
        ]);

        $supportTicket->update(['status' => 'waiting_customer']);

        return back()->with('success', 'تم إرسال ردك بنجاح.');
    }
}
