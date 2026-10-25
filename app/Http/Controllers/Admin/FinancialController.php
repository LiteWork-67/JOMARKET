<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialLedgerEntry;
use App\Models\Seller;
use App\Models\SellerOrder;
use App\Models\SellerPayoutRequest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FinancialController extends Controller
{
    public function commissions(Request $request): View
    {
        $query = SellerOrder::where('status', 'delivered')->with('seller');

        if ($sellerId = $request->input('seller_id')) {
            $query->where('seller_id', $sellerId);
        }

        if ($from = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $totalCommissions = (float) (clone $query)->sum('commission_amount');
        $totalDeliveredSales = (float) (clone $query)->sum('total_amount');
        $sellers = Seller::where('status', 'approved')->orderBy('store_name_ar')->get();

        return view('admin.finance.commissions', compact('orders', 'totalCommissions', 'totalDeliveredSales', 'sellers'));
    }

    public function payouts(Request $request): View
    {
        Gate::authorize('viewAny', SellerPayoutRequest::class);

        $query = SellerPayoutRequest::with(['seller', 'requestedByUser', 'approvedByUser']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $payouts = $query->latest()->paginate(15)->withQueryString();

        return view('admin.finance.payouts', compact('payouts'));
    }

    public function approvePayout(Request $request, int|string $id): RedirectResponse
    {
        $payout = SellerPayoutRequest::with('seller')->findOrFail($id);

        Gate::authorize('approve', $payout);

        if (! in_array($payout->status, ['requested', 'processing'])) {
            return back()->with('error', __('Only pending payout requests can be approved.'));
        }

        $seller = $payout->seller;
        $currentBalance = $seller->getSettledBalance();

        if ($payout->amount > $currentBalance) {
            return back()->with('error', __('Cannot approve payout: requested amount (:req JOD) exceeds seller settled balance (:bal JOD).', [
                'req' => number_format($payout->amount, 3),
                'bal' => number_format($currentBalance, 3),
            ]));
        }

        DB::transaction(function () use ($request, $payout, $seller, $currentBalance) {
            $trxRef = $request->input('transaction_reference', 'BNK-'.strtoupper(Str::random(10)));
            $oldStatus = $payout->status;

            $payout->update([
                'status' => 'completed',
                'approved_by' => auth()->id(),
                'transaction_reference' => $trxRef,
                'processed_at' => now(),
            ]);

            $newBalance = round($currentBalance - (float) $payout->amount, 3);

            FinancialLedgerEntry::create([
                'seller_id' => $seller->id,
                'type' => 'payout',
                'entry_type' => 'debit',
                'amount' => $payout->amount,
                'currency' => 'JOD',
                'balance_after' => $newBalance,
                'reference_type' => 'seller_payout_request',
                'reference_id' => $payout->id,
                'description' => "Settlement payout transfer to {$seller->bank_name} IBAN {$seller->iban} (Ref: {$trxRef})",
                'created_by' => auth()->id(),
            ]);

            AuditService::log('admin.payout.approve', $payout, ['status' => $oldStatus], ['status' => 'completed', 'reference' => $trxRef]);
        });

        return back()->with('success', __('Payout request approved and ledger debited successfully.'));
    }

    public function rejectPayout(Request $request, int|string $id): RedirectResponse
    {
        $payout = SellerPayoutRequest::findOrFail($id);

        Gate::authorize('reject', $payout);

        if (! in_array($payout->status, ['requested', 'processing'])) {
            return back()->with('error', __('Only pending payout requests can be rejected.'));
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $oldValues = $payout->toArray();
        $payout->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'rejection_reason' => $request->input('rejection_reason'),
            'processed_at' => now(),
        ]);

        AuditService::log('admin.payout.reject', $payout, $oldValues, $payout->toArray());

        return back()->with('success', __('Payout request rejected.'));
    }

    public function ledger(Request $request): View
    {
        $query = FinancialLedgerEntry::with(['seller', 'createdByUser']);

        if ($sellerId = $request->input('seller_id')) {
            $query->where('seller_id', $sellerId);
        }

        if ($entryType = $request->input('entry_type')) {
            $query->where('entry_type', $entryType);
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $entries = $query->latest()->paginate(20)->withQueryString();

        $totalCredits = (float) FinancialLedgerEntry::where('entry_type', 'credit')->sum('amount');
        $totalDebits = (float) FinancialLedgerEntry::where('entry_type', 'debit')->sum('amount');
        $sellers = Seller::where('status', 'approved')->orderBy('store_name_ar')->get();

        return view('admin.finance.ledger', compact('entries', 'totalCredits', 'totalDebits', 'sellers'));
    }
}
