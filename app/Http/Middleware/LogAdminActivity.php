<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LogAdminActivity
 * Records friendly admin actions using the `log_event` helper.
 */
class LogAdminActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            // Prepare context
            $admin = Auth::guard('admin')->user();
            // Only record when an admin is authenticated (avoid logging anonymous requests)
            if (!$admin) {
                return $response;
            }

            // Build friendly resource name
            $path = trim($request->path(), '/');
            $segments = explode('/', $path);
            if (!empty($segments) && $segments[0] === 'admin') array_shift($segments);
            $resource = $segments[0] ?? null;
            $friendly = $resource ? str_replace('-', ' ', rtrim($resource, 's')) : 'dashboard';

            // detect id and try to obtain a human-friendly label (from route-model or DB)
            $routeParams = $request->route() ? $request->route()->parameters() : [];
            $id = null;
            $entityLabel = null;
            foreach ($routeParams as $p) {
                if (is_object($p) && isset($p->id)) {
                    $id = $p->id;
                    // try common label properties on bound model
                    if (isset($p->name) && $p->name) $entityLabel = $p->name;
                    elseif (isset($p->title) && $p->title) $entityLabel = $p->title;
                    elseif (isset($p->email) && $p->email) $entityLabel = $p->email;
                    elseif (isset($p->order_number) && $p->order_number) $entityLabel = $p->order_number;
                    break;
                }
                if (is_numeric($p)) {
                    $id = $p;
                    break;
                }
            }
            if (!$id && isset($segments[1]) && is_numeric($segments[1])) $id = $segments[1];

            // If we don't have a label from model binding, try mapping resource -> model and fetch a name
            if (!$entityLabel && $id) {
                $resourcePlural = $segments[0] ?? null; // e.g. 'services'
                $modelMap = [
                    'services' => \App\Models\Service::class,
                    'bookings' => \App\Models\Booking::class,
                    'drivers' => \App\Models\Driver::class,
                    'users' => \App\Models\User::class,
                    'admins' => \App\Models\Admin::class,
                    'labs' => \App\Models\Lab::class,
                    'areas' => \App\Models\Area::class,
                    'home-banners' => \App\Models\HomeBanner::class,
                ];
                if ($resourcePlural && isset($modelMap[$resourcePlural])) {
                    try {
                        $modelClass = $modelMap[$resourcePlural];
                        if (class_exists($modelClass)) {
                            $m = $modelClass::find($id);
                            if ($m) {
                                if (!empty($m->name)) $entityLabel = $m->name;
                                elseif (!empty($m->title)) $entityLabel = $m->title;
                                elseif (!empty($m->email)) $entityLabel = $m->email;
                                elseif (!empty($m->order_number)) $entityLabel = $m->order_number;
                            }
                        }
                    } catch (\Throwable $e) {
                        // ignore DB lookup failures
                    }
                }
            }

            $method = strtoupper($request->method());

            // Do not log simple GET list views for noisy resources (e.g. bookings index)
            // This prevents many repeated "Viewed booking list" entries when the admin
            // navigates to the bookings page.
            $skipListResources = ['bookings'];
            if ($method === 'GET' && in_array($resource, $skipListResources) && !$id) {
                return $response;
            }

            if ($method === 'GET') {
                if ($id) {
                    $action = $entityLabel ? "Viewed {$friendly} \"{$entityLabel}\"" : "Viewed {$friendly} #{$id}";
                } else {
                    $action = "Viewed {$friendly} list";
                }
            } elseif ($method === 'POST') {
                $action = $id ? ($entityLabel ? "Performed POST on {$friendly} \"{$entityLabel}\"" : "Performed POST on {$friendly} #{$id}") : "Created {$friendly}";
            } elseif (in_array($method, ['PUT','PATCH'])) {
                $action = $id ? ($entityLabel ? "Updated {$friendly} \"{$entityLabel}\"" : "Updated {$friendly} #{$id}") : "Updated {$friendly}";
            } elseif ($method === 'DELETE') {
                $action = $id ? ($entityLabel ? "Deleted {$friendly} \"{$entityLabel}\"" : "Deleted {$friendly} #{$id}") : "Deleted {$friendly}";
            } else {
                $action = "Performed {$method} on {$friendly}";
            }

            $inputs = $request->except(['password','password_confirmation','_token','api_token','token']);
            $contextPieces = [];
            foreach (['name','title','email','status'] as $k) {
                if (array_key_exists($k, $inputs) && !empty($inputs[$k])) {
                    $val = is_string($inputs[$k]) ? $inputs[$k] : json_encode($inputs[$k]);
                    $contextPieces[] = "{$k}: {$val}";
                    break;
                }
            }
            $contextStr = $contextPieces ? ' (' . implode(', ', $contextPieces) . ')' : '';

            // Build description: friendly English action (no prefix)
            $description = $action . $contextStr;

            $routeName = $request->route() ? $request->route()->getName() : null;

            // Skip logging for specific routes that shouldn't be stored (e.g., FCM token registration)
            $skipRoutes = [
                'admin.fcm-token.store',
            ];
            if ($routeName && in_array($routeName, $skipRoutes)) {
                return $response;
            }
            // Also skip by resource + method as a fallback
            if (($resource === 'fcm-token' || $resource === 'fcm_token') && strtoupper($request->method()) === 'POST') {
                return $response;
            }

            // Store the log with explicit actor info so actor_name and actor_id are filled
            \App\Services\SystemLogger::record([
                'actor_type' => 'admin',
                'actor_id' => $admin->id,
                'actor_name' => $admin->email ?? ($admin->name ?? null),
                'event_type' => 'admin_route',
                'event_subtype' => $routeName ?? $resource,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => [
                    'description' => $description,
                    'method' => $method,
                    'uri' => $request->getRequestUri(),
                    'inputs' => empty($inputs) ? null : array_slice($inputs, 0, 20),
                    'route_name' => $routeName,
                ],
            ]);

        } catch (\Throwable $e) {
            logger()->warning('LogAdminActivity failed: ' . $e->getMessage());
        }

        return $response;
    }
}
