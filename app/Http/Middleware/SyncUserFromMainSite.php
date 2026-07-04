<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SyncUserFromMainSite
{
    /**
     * Handle an incoming request.
     * Check if user is authenticated in shared session from main site
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated in POS but has session from main site
        if (!Auth::check()) {
            $sessionId = $request->session()->getId();
            
            // Query the sessions table directly to get user_id
            // This works because both apps share the same database and session table
            $sessionData = DB::table('sessions')
                ->where('id', $sessionId)
                ->first();
            
            if ($sessionData && $sessionData->user_id) {
                // Find user and authenticate
                $user = \App\Models\User::find($sessionData->user_id);
                
                if ($user) {
                    // Check if user has is_active field
                    if (!isset($user->is_active) || $user->is_active) {
                        // Login without triggering events to keep shared session intact
                        Auth::guard('web')->loginUsingId($user->id, false);
                        
                        Log::info('User synced from main site', [
                            'user_id' => $user->id,
                            'session_id' => $sessionId,
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
