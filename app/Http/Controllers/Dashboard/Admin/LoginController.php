<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('dashboard.admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $fcmToken = $request->input('fcm_token');

        // Log incoming login attempt (never log raw password)
        \Illuminate\Support\Facades\Log::info('Admin login attempt', [
            'email' => $request->input('email'),
            'has_fcm_token' => !empty($fcmToken),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Persist to system logs
        try {
            \App\Services\SystemLogger::record([
                'actor_type' => 'admin',
                'actor_id' => null,
                'actor_name' => $request->input('email'),
                'event_type' => 'auth',
                'event_subtype' => 'login_attempt',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'description' => 'Admin login attempt',
                    'email' => $request->input('email'),
                    'has_fcm_token' => !empty($fcmToken),
                ],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to write system log for admin login attempt: ' . $e->getMessage());
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            // Save FCM token if provided
            if ($fcmToken) {
                try {
                    $admin = Auth::guard('admin')->user();
                    if ($admin) {
                        $admin->fcm_token = $fcmToken;
                        $admin->save();
                        \Illuminate\Support\Facades\Log::info('Saved admin fcm_token on login', [
                            'admin_id' => $admin->id,
                            'email' => $admin->email,
                            'fcm_token' => $fcmToken,
                        ]);
                        try {
                            \App\Services\SystemLogger::record([
                                'actor_type' => 'admin',
                                'actor_id' => $admin->id,
                                'actor_name' => $admin->name ?? $admin->email,
                                'event_type' => 'auth',
                                'event_subtype' => 'login_success',
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                                'payload' => [ 'description' => 'Login successful', 'email' => $admin->email, 'has_fcm_token' => true ],
                            ]);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::warning('Failed to write system log after admin login: ' . $e->getMessage());
                        }
                    } else {
                        \Illuminate\Support\Facades\Log::warning('Admin authenticated but user() returned null when saving fcm_token', [
                            'email' => $request->input('email'),
                        ]);
                    }
                } catch (\Exception $e) {
                    // log but don't block login
                    \Illuminate\Support\Facades\Log::warning('Failed to save admin fcm_token on login', [
                        'error' => $e->getMessage(),
                        'email' => $request->input('email'),
                        'fcm_token' => $fcmToken,
                    ]);
                }
            } else {
                \Illuminate\Support\Facades\Log::info('Admin logged in without fcm_token', ['email' => $request->input('email')]);
            }

            \Illuminate\Support\Facades\Log::info('Admin login successful', ['email' => $request->input('email')]);

            // In case admin was authenticated but fcm handling did not write the success log above
            try {
                $admin = Auth::guard('admin')->user();
                \App\Services\SystemLogger::record([
                    'actor_type' => 'admin',
                    'actor_id' => $admin ? $admin->id : null,
                    'actor_name' => $admin ? ($admin->name ?? $admin->email) : $request->input('email'),
                    'event_type' => 'auth',
                    'event_subtype' => 'login_success',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'payload' => ['description' => 'Login successful', 'email' => $admin ? $admin->email : $request->input('email'), 'has_fcm_token' => !empty($fcmToken)],
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to write system log after admin login (fallback): ' . $e->getMessage());
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}