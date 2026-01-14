<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * TrackAdminOnline
 *
 * Stores an "online" flag in the cache for the authenticated admin.
 * The flag expires after 5 minutes and is refreshed on each request.
 */
class TrackAdminOnline
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (Auth::guard('admin')->check()) {
                $admin = Auth::guard('admin')->user();
                if ($admin && isset($admin->id)) {
                    $id = $admin->id;
                    // Key to indicate online status (expires in 5 minutes)
                    $onlineKey = "admin_online:{$id}";
                    Cache::put($onlineKey, true, now()->addMinutes(5));

                    // Keep a last-seen timestamp for display (optional)
                    $lastSeenKey = "admin_last_seen:{$id}";
                    Cache::put($lastSeenKey, now()->toDateTimeString(), now()->addDays(7));
                }
            }
        } catch (\Throwable $e) {
            // Do not break the request lifecycle if cache or auth fails
            logger()->warning('TrackAdminOnline middleware failed: ' . $e->getMessage());
        }

        return $next($request);
    }
}
