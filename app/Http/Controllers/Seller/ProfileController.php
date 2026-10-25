<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\UpdateSellerProfileRequest;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;

        Gate::authorize('view', $seller);

        return view('seller.profile.edit', compact('seller'));
    }

    public function update(UpdateSellerProfileRequest $request): RedirectResponse
    {
        $seller = $request->get('current_seller') ?? $request->user()->seller;

        Gate::authorize('update', $seller);

        $oldValues = $seller->toArray();
        $validated = $request->validated();

        $seller->update($validated);

        AuditService::log('seller.profile.update', $seller, $oldValues, $seller->toArray());

        return back()->with('success', __('Store profile updated successfully.'));
    }
}
