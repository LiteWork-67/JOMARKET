<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSupport
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isSupport() && ! $user->isAdmin())) {
            abort(403, 'Unauthorized access to JordanMart Support Panel.');
        }

        return $next($request);
    }
}
