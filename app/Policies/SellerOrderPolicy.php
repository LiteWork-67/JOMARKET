<?php

namespace App\Policies;

use App\Models\SellerOrder;
use App\Models\User;

class SellerOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSeller() || $user->isAdmin() || $user->isSupport();
    }

    public function view(User $user, SellerOrder $sellerOrder): bool
    {
        if ($user->isAdmin() || $user->isSupport()) {
            return true;
        }

        return $user->seller && $sellerOrder->seller_id === $user->seller->id;
    }

    public function update(User $user, SellerOrder $sellerOrder): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->seller && $sellerOrder->seller_id === $user->seller->id;
    }
}
