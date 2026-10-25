<?php

namespace App\Policies;

use App\Models\SellerOffer;
use App\Models\User;

class SellerOfferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSeller() || $user->isAdmin();
    }

    public function view(User $user, SellerOffer $offer): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->seller && $offer->seller_id === $user->seller->id;
    }

    public function create(User $user): bool
    {
        return $user->isSeller() && $user->seller && $user->seller->isApproved();
    }

    public function update(User $user, SellerOffer $offer): bool
    {
        return $user->seller && $offer->seller_id === $user->seller->id;
    }

    public function delete(User $user, SellerOffer $offer): bool
    {
        return $user->seller && $offer->seller_id === $user->seller->id;
    }
}
