<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\Lab as LabModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('lab.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Resolve account by email if possible so we can always log the account info
        $email = $request->input('email');
        $account = null;
        try {
            $account = LabModel::where('email', $email)->first();
        } catch (\Throwable $e) {
            // ignore lookup failures
        }

        // Record attempt (include resolved account info when available)
        try {
            \App\Services\SystemLogger::record([
                'actor_type' => 'lab',
                'actor_id' => $account ? $account->id : null,
                'actor_name' => $account ? ($account->name ?? $account->email) : $email,
                'event_type' => 'auth',
                'event_subtype' => 'login_attempt',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'description' => "Login attempt for lab account",
                    'email' => $email,
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to write system log for lab login attempt: ' . $e->getMessage());
        }

        if (Auth::guard('lab')->attempt($credentials)) {
            $request->session()->regenerate();
            try {
                $user = Auth::guard('lab')->user();
                \App\Services\SystemLogger::record([
                    'actor_type' => 'lab',
                    'actor_id' => $user ? $user->id : ($account ? $account->id : null),
                    'actor_name' => $user ? ($user->name ?? $user->email) : ($account ? ($account->name ?? $account->email) : $email),
                    'event_type' => 'auth',
                    'event_subtype' => 'login_success',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'payload' => [
                        'description' => 'Login successful',
                        'email' => $email,
                    ],
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to write system log after lab login: ' . $e->getMessage());
            }
            return redirect()->route('lab.dashboard');
        }

        // failed login - log failure with description
        try {
            \App\Services\SystemLogger::record([
                'actor_type' => 'lab',
                'actor_id' => $account ? $account->id : null,
                'actor_name' => $account ? ($account->name ?? $account->email) : $email,
                'event_type' => 'auth',
                'event_subtype' => 'login_failed',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'description' => 'Login failed - invalid credentials',
                    'email' => $email,
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to write system log for failed lab login: ' . $e->getMessage());
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::guard('lab')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('lab.login');
    }
}
