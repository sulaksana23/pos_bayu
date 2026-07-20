<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * NOTE: Subscription checking is currently disabled.
     * All authenticated users have full access to POS features.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
