<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $priority = $request->query('priority');

        $tickets = SupportTicket::with(['user', 'parentOrder'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($priority, fn ($q) => $q->where('priority', $priority))
            ->latest()
            ->paginate(15);

        return view('admin.support.index', [
            'tickets' => $tickets,
            'currentStatus' => $status,
            'currentPriority' => $priority,
        ]);
    }

    public function show(SupportTicket $supportTicket): View
    {
        $supportTicket->load(['user', 'seller', 'parentOrder', 'messages.sender']);

        return view('admin.support.show', [
            'ticket' => $supportTicket,
        ]);
    }

    public function reply(SupportTicket $supportTicket, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2'],
            'is_internal_note' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:open,in_progress,waiting_customer,resolved,closed'],
        ]);

        $isInternal = $request->boolean('is_internal_note');

        SupportTicketMessage::create([
            'support_ticket_id' => $supportTicket->id,
            'sender_id' => Auth::id(),
            'message' => $validated['message'],
            'is_internal_note' => $isInternal,
        ]);

        if (! empty($validated['status'])) {
            $supportTicket->update(['status' => $validated['status']]);
        } elseif (! $isInternal) {
            $supportTicket->update(['status' => 'in_progress']);
        }

        AuditService::log(
            'support_ticket_reply',
            'support_tickets',
            $supportTicket->id,
            ['is_internal' => $isInternal, 'status' => $supportTicket->status]
        );

        return back()->with('success', 'تم تسجيل الرد بنجاح.');
    }

    public function updateStatus(SupportTicket $supportTicket, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,waiting_customer,resolved,closed'],
        ]);

        $oldStatus = $supportTicket->status;
        $supportTicket->update(['status' => $validated['status']]);

        AuditService::log(
            'support_ticket_status_change',
            'support_tickets',
            $supportTicket->id,
            ['from' => $oldStatus, 'to' => $validated['status']]
        );

        return back()->with('success', 'تم تحديث حالة التذكرة إلى: '.$validated['status']);
    }
}
