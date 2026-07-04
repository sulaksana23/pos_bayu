<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowIframeForDemo
{
    /**
     * Handle an incoming request.
     * Remove X-Frame-Options header to allow embedding in iframe.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Override X-Frame-Options with ALLOWALL value (will allow iframe)
        // Some proxies/CDN respect this over removal
        $response->headers->set('X-Frame-Options', 'ALLOWALL', true);
        
        // Set Content-Security-Policy to allow embedding from main domain
        $response->headers->set(
            'Content-Security-Policy',
            "frame-ancestors 'self' https://balitechsolution.com https://www.balitechsolution.com",
            true
        );
        
        // Send a custom header to verify middleware is working
        $response->headers->set('X-Demo-Iframe-Allowed', 'true', true);
        
        return $response;
    }
}
