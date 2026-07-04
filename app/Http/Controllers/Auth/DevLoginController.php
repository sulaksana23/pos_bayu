<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DevLoginController extends Controller
{
    public function __invoke(string $email): RedirectResponse
    {
        if (!app()->environment(['local', 'development'])) {
            abort(404);
        }

        $user = User::where('email', $email)->firstOrFail();
        Auth::login($user);
        session()->regenerate();

        return redirect()->route('pos.dashboard');
    }
}
