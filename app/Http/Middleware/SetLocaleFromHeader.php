<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check multiple possible sources for locale in order of priority
        $locale = $request->header('Accept-Language') ??
                 $request->query('lang') ??
                 session('locale') ??
                 config('app.fallback_locale', 'en');

        // Clean and validate locale
        $locale = substr(strtolower(trim($locale)), 0, 2);

        // Get supported locales from config
        $supportedLocales = config('app.supported_locales', ['en', 'ar']);

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        } else {
            App::setLocale(config('app.fallback_locale', 'en'));
        }

        // Add locale to response headers for API consistency
        $response = $next($request);
        if (method_exists($response, 'header')) {
            $response->header('Content-Language', App::getLocale());
        }

        return $response;
    }
}
