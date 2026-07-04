<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return redirect()->route('pos.dashboard');
        }
        // No login page on this subdomain — send to main website
        return redirect(config('app.main_url', 'https://balitechsolution.com').'/login');
    }
}
