<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use App\Http\Middleware\UpdateAdminStatus;
use App\Http\Middleware\EnsureUserIsAuthenticated;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware (applies on all requests)
        $middleware->append(SetLocale::class);

        // Aliases (apply by name)
        $middleware->alias([
            'auth.api' => EnsureUserIsAuthenticated::class,
            'update.admin.status' => UpdateAdminStatus::class, // New middleware
            // Admin online tracking
            'track.admin.online' => \App\Http\Middleware\TrackAdminOnline::class,
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            // Spatie Permission middleware aliases
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Render friendly alert for permission denied / 403 responses
        $exceptions->renderable(function (\Throwable $e, $request) {
            // Spatie unauthorized exception
            if ($e instanceof \Spatie\Permission\Exceptions\UnauthorizedException
                || $e instanceof \Illuminate\Auth\Access\AuthorizationException
                || ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 403)) {

                $message = "You don't have this action in your permissions. Return to the admin dashboard.";

                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json(['message' => $message], 403);
                }

                // Try to redirect back, fall back to admin dashboard
                $back = url()->previous() ?: (\Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('/'));

                return redirect($back)->with('error', $message)->with('custom_alert', $message);
            }

            return null; // let the default handler run
        });
    })->create();
