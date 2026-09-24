<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     */
    public const SUPPORTED_LOCALES = ['id', 'en', 'ja', 'zh'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('locale')
            ?? ($request->hasSession() ? $request->session()->get('locale') : null)
            ?? ($_COOKIE['locale'] ?? null)
            ?? config('app.locale', 'id');

        if (! in_array($locale, self::SUPPORTED_LOCALES, true)) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
