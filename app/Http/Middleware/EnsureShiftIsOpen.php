<?php
namespace App\Http\Middleware;

use App\Models\PosShift;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureShiftIsOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect(config('app.main_url', 'https://balitechsolution.com').'/login');
        }
        if (! $user->currentShift()) {
            return redirect()
                ->route('pos.shifts.open')
                ->with('warning', 'Buka shift terlebih dahulu sebelum melanjutkan transaksi.');
        }
        return $next($request);
    }
}
