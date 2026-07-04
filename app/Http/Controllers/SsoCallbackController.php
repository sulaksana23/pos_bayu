<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoCallbackController extends Controller
{
    public function callback(Request $request)
    {
        $token = $request->query('token');

        if (!$token || strlen($token) !== 64) {
            return redirect(config('services.dashboard.url'))
                ->with('error', 'Token SSO tidak valid');
        }

        try {
            // Use internal URL for server-to-server communication (avoids public internet)
            $internalUrl = config('services.dashboard.internal_url', config('services.dashboard.url'));

            $response = Http::timeout(10)
                ->withHeaders([
                    'X-SSO-Secret'       => config('services.sso.shared_secret'),
                    'Accept'             => 'application/json',
                    'Host'               => parse_url(config('services.dashboard.url'), PHP_URL_HOST),
                    'X-Forwarded-Proto'  => 'https',
                ])
                ->post($internalUrl . '/sso/verify', [
                    'token' => $token,
                ]);

            if (!$response->successful()) {
                Log::warning('SSO verification failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return redirect(config('services.dashboard.url'))
                    ->with('error', 'Token SSO tidak valid atau sudah kadaluarsa');
            }

            $data = $response->json();
            $userData = $data['user'] ?? null;

            if (!$userData || !isset($userData['email'])) {
                return redirect(config('services.dashboard.url'))
                    ->with('error', 'Data user tidak valid');
            }

            // Find or create user
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make(Str::random(32)), // Random password
                    'role' => $userData['role'] ?? 'user',
                ]);
            } else {
                // Update user info from dashboard
                $user->update([
                    'name' => $userData['name'],
                    'role' => $userData['role'] ?? $user->role,
                ]);
            }

            // Login user
            Auth::login($user);

            return redirect()->route('pos.dashboard')
                ->with('success', 'Berhasil login via SSO');

        } catch (\Exception $e) {
            Log::error('SSO callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect(config('services.dashboard.url'))
                ->with('error', 'Terjadi kesalahan saat proses SSO');
        }
    }
}
