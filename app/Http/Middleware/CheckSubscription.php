<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * POS access is granted to users with role 'cashier', 'manager', or 'admin',
     * OR users with an active subscription (pro/team plan) in the shared database.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->away(config('app.main_url', 'https://balitechsolution.com') . '/login');
        }

        // Role-based access: cashier, manager, and admin always have POS access
        if (in_array($user->role, ['admin', 'manager', 'cashier'], true)) {
            return $next($request);
        }

        // Subscription-based access: check shared subscriptions table
        if ($this->hasActivePosSubscription($user->id)) {
            return $next($request);
        }

        return redirect()->away(
            config('app.main_url', 'https://balitechsolution.com') . '/?pos_error=subscription_required'
        );
    }

    /**
     * Check if user has an active POS-eligible subscription (pro or team plan).
     * Both apps share the same PostgreSQL database, so no second connection needed.
     */
    private function hasActivePosSubscription(int $userId): bool
    {
        try {
            return DB::table('subscriptions')
                ->join('subscription_plans', 'subscriptions.plan_id', '=', 'subscription_plans.id')
                ->where('subscriptions.user_id', $userId)
                ->where('subscriptions.status', 'active')
                ->where('subscriptions.expires_at', '>', now())
                ->whereIn('subscription_plans.slug', ['pro', 'team'])
                ->exists();
        } catch (\Exception $e) {
            dd([
                'error'   => $e->getMessage(),
                'class'   => get_class($e),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => $userId,
            ]);
        }
    }
}
