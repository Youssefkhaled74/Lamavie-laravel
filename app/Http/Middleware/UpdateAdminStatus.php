<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateAdminStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            $admin->update([
                'is_online' => true,
                'last_login_at' => now(),
            ]);

            // Optionally, schedule a job to set is_online to false after 5 minutes
            // This requires a queue setup (e.g., database, redis)
        }

        return $next($request);
    }
}