<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StorePayoutRequest;
use App\Models\SellerPayoutRequest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PayoutController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SellerPayoutRequest::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;

        $payouts = SellerPayoutRequest::where('seller_id', $seller->id)
            ->with(['requestedByUser', 'approvedByUser'])
            ->latest()
            ->paginate(15);

        $settledBalance = $seller->getSettledBalance();
        $pendingPayoutsSum = (float) SellerPayoutRequest::where('seller_id', $seller->id)
            ->whereIn('status', ['requested', 'processing'])
            ->sum('amount');
        $availableForPayout = max(0, round($settledBalance - $pendingPayoutsSum, 3));

        return view('seller.payouts.index', compact('payouts', 'seller', 'settledBalance', 'pendingPayoutsSum', 'availableForPayout'));
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', SellerPayoutRequest::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $settledBalance = $seller->getSettledBalance();
        $pendingPayoutsSum = (float) SellerPayoutRequest::where('seller_id', $seller->id)
            ->whereIn('status', ['requested', 'processing'])
            ->sum('amount');
        $availableForPayout = max(0, round($settledBalance - $pendingPayoutsSum, 3));

        return view('seller.payouts.create', compact('seller', 'settledBalance', 'availableForPayout'));
    }

    public function store(StorePayoutRequest $request): RedirectResponse
    {
        Gate::authorize('create', SellerPayoutRequest::class);

        $seller = $request->get('current_seller') ?? $request->user()->seller;
        $amount = (float) $request->input('amount');

        $settledBalance = $seller->getSettledBalance();
        $pendingPayoutsSum = (float) SellerPayoutRequest::where('seller_id', $seller->id)
            ->whereIn('status', ['requested', 'processing'])
            ->sum('amount');
        $availableForPayout = max(0, round($settledBalance - $pendingPayoutsSum, 3));

        if ($amount > $availableForPayout) {
            return back()->withInput()->withErrors([
                'amount' => __('The requested payout amount (:amount JOD) exceeds your available balance (:available JOD).', [
                    'amount' => number_format($amount, 3),
                    'available' => number_format($availableForPayout, 3),
                ]),
            ]);
        }

        $payout = SellerPayoutRequest::create([
            'seller_id' => $seller->id,
            'amount' => $amount,
            'currency' => 'JOD',
            'status' => 'requested',
            'requested_by' => auth()->id(),
            'notes' => $request->input('notes'),
        ]);

        AuditService::log('seller.payout.request', $payout, null, $payout->toArray());

        return redirect()->route('seller.payouts.index')
            ->with('success', __('Payout request submitted successfully and is awaiting administrator review.'));
    }
}
