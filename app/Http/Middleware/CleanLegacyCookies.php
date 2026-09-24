<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanLegacyCookies
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $legacyCookies = [
            'corporate-ticketing-session',
            'laravel_session',
            'laravel-session',
        ];

        foreach ($legacyCookies as $cookieName) {
            if ($request->cookies->has($cookieName)) {
                $response->headers->setCookie(cookie()->forget($cookieName, '/', null));
            }
        }

        return $response;
    }
}
