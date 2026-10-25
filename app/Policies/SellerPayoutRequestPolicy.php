<?php

namespace App\Policies;

use App\Models\SellerPayoutRequest;
use App\Models\User;

class SellerPayoutRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isSeller();
    }

    public function view(User $user, SellerPayoutRequest $payout): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->seller && $payout->seller_id === $user->seller->id;
    }

    public function create(User $user): bool
    {
        return $user->isSeller() && $user->seller && $user->seller->isApproved();
    }

    public function approve(User $user, SellerPayoutRequest $payout): bool
    {
        return $user->isAdmin() && $user->hasPermission('sellers.payouts');
    }

    public function reject(User $user, SellerPayoutRequest $payout): bool
    {
        return $user->isAdmin() && $user->hasPermission('sellers.payouts');
    }
}
