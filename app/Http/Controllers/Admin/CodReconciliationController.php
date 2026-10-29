<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashOnDeliveryReconciliation;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CodReconciliationController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $reconciliations = CashOnDeliveryReconciliation::with(['sellerOrder.seller', 'sellerOrder.parentOrder'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        $stats = [
            'total_pending' => CashOnDeliveryReconciliation::where('status', 'pending_collection')->sum('amount_collected'),
            'total_collected' => CashOnDeliveryReconciliation::where('status', 'collected_by_courier')->sum('amount_collected'),
            'total_remitted' => CashOnDeliveryReconciliation::where('status', 'remitted_to_platform')->sum('amount_collected'),
            'total_settled' => CashOnDeliveryReconciliation::where('status', 'settled_to_seller')->sum('amount_collected'),
        ];

        return view('admin.finance.cod-reconciliation', [
            'reconciliations' => $reconciliations,
            'stats' => $stats,
            'currentStatus' => $status,
        ]);
    }

    public function updateStatus(CashOnDeliveryReconciliation $reconciliation, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending_collection,collected_by_courier,remitted_to_platform,settled_to_seller'],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $updates = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $reconciliation->notes,
        ];

        if (! empty($validated['courier_name'])) {
            $updates['courier_name'] = $validated['courier_name'];
        }

        if ($validated['status'] === 'collected_by_courier' && ! $reconciliation->collected_at) {
            $updates['collected_at'] = now();
        }

        if ($validated['status'] === 'remitted_to_platform' && ! $reconciliation->remitted_at) {
            $updates['remitted_at'] = now();
        }

        $reconciliation->update($updates);

        AuditService::log(
            'cod_reconciliation_status_change',
            'cash_on_delivery_reconciliations',
            $reconciliation->id,
            ['status' => $validated['status']]
        );

        return back()->with('success', 'تم تحديث حالة تسوية الدفع عند الاستلام بنجاح.');
    }
}
