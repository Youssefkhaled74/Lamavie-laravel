<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Priority: explicit query param -> session -> cookie -> Accept-Language header -> default
        $candidate = null;

        if ($request->query('locale')) {
            $candidate = $request->query('locale');
            // persist user's choice in session and cookie for subsequent requests
            session(['locale' => $candidate]);
            cookie()->queue(cookie('locale', $candidate, 60 * 24 * 30));
        } elseif (session('locale')) {
            $candidate = session('locale');
        } elseif ($request->cookie('locale')) {
            $candidate = $request->cookie('locale');
        } else {
            $candidate = $request->header('Accept-Language', 'en');
        }

        $locale = in_array($candidate, ['en', 'ar']) ? $candidate : 'en';
        App::setLocale($locale);

        return $next($request);
    }
}
