<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedgerEntry;
use App\Models\InventoryMovement;
use App\Models\Refund;
use App\Models\ReturnRequest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DisputeAndRefundController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $returnRequests = ReturnRequest::with(['user', 'sellerOrder.seller', 'sellerOrder.parentOrder', 'items.orderItem.product'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return view('admin.disputes.index', [
            'returnRequests' => $returnRequests,
            'currentStatus' => $status,
        ]);
    }

    public function show(ReturnRequest $returnRequest): View
    {
        $returnRequest->load([
            'user',
            'sellerOrder.seller',
            'sellerOrder.parentOrder',
            'items.orderItem.product',
        ]);

        return view('admin.disputes.show', [
            'dispute' => $returnRequest,
        ]);
    }

    public function process(ReturnRequest $returnRequest, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,mark_received,process_refund'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
            'refund_amount' => ['nullable', 'numeric', 'min:0.001'],
        ]);

        $adminId = Auth::id();
        $action = $validated['action'];
        $notes = $validated['admin_notes'] ?? null;

        DB::transaction(function () use ($returnRequest, $action, $notes, $adminId, $validated) {
            $sellerOrder = $returnRequest->sellerOrder;
            $parentOrder = $sellerOrder->parentOrder;

            switch ($action) {
                case 'approve':
                    $returnRequest->update([
                        'status' => 'approved',
                        'processed_by' => $adminId,
                        'admin_notes' => $notes,
                    ]);
                    break;

                case 'reject':
                    $returnRequest->update([
                        'status' => 'rejected',
                        'processed_by' => $adminId,
                        'admin_notes' => $notes,
                    ]);
                    $sellerOrder->update(['status' => 'delivered']);
                    break;

                case 'mark_received':
                    $returnRequest->update([
                        'status' => 'item_received',
                        'processed_by' => $adminId,
                        'admin_notes' => $notes,
                    ]);
                    $sellerOrder->update(['status' => 'returned']);

                    // Restock returned items
                    foreach ($returnRequest->items as $item) {
                        $orderItem = $item->orderItem;
                        $offer = $orderItem->offer;
                        if ($offer) {
                            $offer->increment('stock_quantity', $item->quantity);
                            $balanceAfter = $offer->fresh()->available_stock;

                            InventoryMovement::create([
                                'seller_offer_id' => $offer->id,
                                'type' => 'refund',
                                'quantity' => $item->quantity,
                                'balance_after' => $balanceAfter,
                                'reference_type' => ReturnRequest::class,
                                'reference_id' => $returnRequest->id,
                                'notes' => 'Restocked via Return Request #'.$returnRequest->id,
                                'created_by' => $adminId,
                            ]);
                        }
                    }
                    break;

                case 'process_refund':
                    $amount = $validated['refund_amount'] ?? (float) $sellerOrder->total_amount;

                    $returnRequest->update([
                        'status' => 'refunded',
                        'processed_by' => $adminId,
                        'admin_notes' => $notes,
                    ]);

                    $sellerOrder->update(['status' => 'refunded']);

                    // 1. Create Refund Record
                    Refund::create([
                        'parent_order_id' => $parentOrder->id,
                        'seller_order_id' => $sellerOrder->id,
                        'amount' => $amount,
                        'currency' => 'JOD',
                        'status' => 'completed',
                        'refund_method' => $parentOrder->payment_method,
                        'transaction_reference' => 'REF-'.strtoupper(Str::random(8)),
                        'reason' => 'Customer return request #'.$returnRequest->id.($notes ? ' — '.$notes : ''),
                        'processed_by' => $adminId,
                    ]);

                    // 2. Financial Ledger Entry - Debit from Seller for Refund
                    $seller = $sellerOrder->seller;
                    $lastEntry = FinancialLedgerEntry::where('seller_id', $seller->id)->latest('id')->first();
                    $currentBalance = $lastEntry ? (float) $lastEntry->balance_after : 0.000;
                    $newBalance = max(0, round($currentBalance - $amount, 3));

                    FinancialLedgerEntry::create([
                        'seller_id' => $seller->id,
                        'type' => 'refund_deduction',
                        'entry_type' => 'debit',
                        'amount' => $amount,
                        'currency' => 'JOD',
                        'balance_after' => $newBalance,
                        'reference_type' => ReturnRequest::class,
                        'reference_id' => $returnRequest->id,
                        'description' => 'Refund deduction for returned order #'.$sellerOrder->order_number,
                        'created_by' => $adminId,
                    ]);

                    break;
            }

            AuditService::log(
                'return_request_'.$action,
                'return_requests',
                $returnRequest->id,
                ['action' => $action, 'notes' => $notes]
            );
        });

        return back()->with('success', 'تم تنفيذ الإجراء بنجاح: '.$action);
    }
}
