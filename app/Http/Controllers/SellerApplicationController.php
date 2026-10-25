<?php

namespace App\Http\Controllers;

use App\Http\Requests\Seller\StoreSellerApplicationRequest;
use App\Models\Seller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SellerApplicationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->seller) {
            if ($user->seller->isApproved()) {
                return redirect()->route('seller.dashboard');
            }

            return view('storefront.seller-application-status', [
                'seller' => $user->seller,
            ]);
        }

        return view('storefront.seller-application');
    }

    public function store(StoreSellerApplicationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->seller) {
            return back()->with('error', __('You already have a registered seller application.'));
        }

        $validated = $request->validated();
        $validated['user_id'] = $user->id;
        $validated['slug'] = Str::slug($validated['store_name_en'] ?: $validated['store_name_ar']).'-'.Str::random(5);
        $validated['status'] = 'pending';
        $validated['commission_rate'] = 10.00;

        $seller = Seller::create($validated);

        AuditService::log('seller.application.submit', $seller, null, $seller->toArray());

        return redirect()->route('seller.apply')
            ->with('success', __('Your seller application has been submitted and is currently under review by our operations team.'));
    }
}
