<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateOrderStatusRequest;
use App\Models\CashOnDeliveryReconciliation;
use App\Models\FinancialLedgerEntry;
use App\Models\OrderStatusHistory;
use App\Models\SellerOrder;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SellerOrder::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;

        $query = SellerOrder::where('seller_id', $seller->id)
            ->with(['parentOrder.user', 'items.product']);

        if ($search = $request->input('search')) {
            $query->where('order_number', 'like', "%{$search}%");
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('seller.orders.index', compact('orders', 'seller'));
    }

    public function show(Request $request, int|string $id): View
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $order = SellerOrder::where('seller_id', $seller->id)
            ->with(['parentOrder.user', 'items.product', 'items.variant', 'statusHistories.changedByUser'])
            ->findOrFail($id);

        Gate::authorize('view', $order);

        return view('seller.orders.show', compact('order', 'seller'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, int|string $id): RedirectResponse
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $order = SellerOrder::where('seller_id', $seller->id)->findOrFail($id);

        Gate::authorize('update', $order);

        $fromStatus = $order->status;
        $toStatus = $request->input('status');

        if ($fromStatus === $toStatus) {
            return back()->with('info', __('Order is already in this status.'));
        }

        DB::transaction(function () use ($request, $seller, $order, $fromStatus, $toStatus) {
            $updateData = [
                'status' => $toStatus,
                'seller_notes' => $request->input('seller_notes', $order->seller_notes),
            ];

            if ($toStatus === 'shipped') {
                $updateData['courier_name'] = $request->input('courier_name');
                $updateData['tracking_number'] = $request->input('tracking_number');
                $updateData['shipped_at'] = now();
            } elseif ($toStatus === 'delivered') {
                $updateData['delivered_at'] = now();
            }

            $order->update($updateData);

            OrderStatusHistory::create([
                'seller_order_id' => $order->id,
                'status_from' => $fromStatus,
                'status_to' => $toStatus,
                'changed_by' => auth()->id(),
                'notes' => $request->input('seller_notes'),
            ]);

            // When delivered, credit seller settled balance if not previously credited
            if ($toStatus === 'delivered') {
                $alreadyCredited = FinancialLedgerEntry::where('seller_id', $seller->id)
                    ->where('reference_type', 'seller_order')
                    ->where('reference_id', $order->id)
                    ->where('entry_type', 'credit')
                    ->exists();

                if (! $alreadyCredited) {
                    $netAmount = (float) $order->seller_net_amount;
                    $currentBalance = $seller->getSettledBalance();
                    $newBalance = round($currentBalance + $netAmount, 3);

                    FinancialLedgerEntry::create([
                        'seller_id' => $seller->id,
                        'type' => 'sale',
                        'entry_type' => 'credit',
                        'amount' => $netAmount,
                        'currency' => 'JOD',
                        'balance_after' => $newBalance,
                        'reference_type' => 'seller_order',
                        'reference_id' => $order->id,
                        'description' => "Earnings for order #{$order->order_number} (Subtotal: {$order->subtotal} JOD, Commission: -{$order->commission_amount} JOD)",
                        'created_by' => auth()->id(),
                    ]);
                }

                // If parent order was Cash on Delivery, ensure COD Reconciliation entry exists
                if ($order->parentOrder->payment_method === 'cash_on_delivery') {
                    CashOnDeliveryReconciliation::firstOrCreate(
                        ['seller_order_id' => $order->id],
                        [
                            'courier_name' => $order->courier_name ?? 'Local Delivery Express',
                            'amount_collected' => $order->total_amount,
                            'status' => 'collected_by_courier',
                            'collected_at' => now(),
                        ]
                    );
                }
            }

            AuditService::log('seller.order.status_update', $order, ['status' => $fromStatus], ['status' => $toStatus]);
        });

        return back()->with('success', __('Order status updated successfully.'));
    }
}
