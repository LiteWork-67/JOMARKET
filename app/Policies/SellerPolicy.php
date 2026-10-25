<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;

class SellerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Seller $seller): bool
    {
        return $user->isAdmin() || $seller->user_id === $user->id;
    }

    public function update(User $user, Seller $seller): bool
    {
        return $user->isAdmin() || $seller->user_id === $user->id;
    }

    public function manage(User $user, Seller $seller): bool
    {
        return $user->isAdmin() && $user->hasPermission('sellers.manage');
    }
}
