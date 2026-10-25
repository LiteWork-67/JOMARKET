<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized access to JordanMart Seller Portal.');
        }

        $seller = $user->seller;

        if (! $seller || ! $seller->isApproved()) {
            abort(403, 'Your seller account is not approved or active.');
        }

        $request->merge(['current_seller' => $seller]);

        return $next($request);
    }
}
