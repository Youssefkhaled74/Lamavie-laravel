<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponse;



class EnsureUserIsAuthenticated
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('sanctum')->check()) {
            return $this->errorResponse(401, 'Unauthenticated');
        }

        return $next($request);
    }
}
