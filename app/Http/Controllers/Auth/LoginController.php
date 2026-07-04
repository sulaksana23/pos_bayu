<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required','string','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $credentials['email'])->where('is_active', true)->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('pos.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
