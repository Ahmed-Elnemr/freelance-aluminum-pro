<?php

namespace App\Http\Middleware;

use App;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = config('app.supported_locales', ['en', 'ar']);
        $locale = $this->resolveLocale($request, $supportedLocales);

        if (in_array($locale, $supportedLocales, true)) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        } else {
            App::setLocale(config('app.fallback_locale', 'en'));
        }

        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('Content-Language', App::getLocale());
        }

        return $response;
    }

    /**
     * @param  list<string>  $supportedLocales
     */
    private function resolveLocale(Request $request, array $supportedLocales): string
    {
        $langHeader = $this->normalizeLocale($request->header('lang'));

        if (in_array($langHeader, $supportedLocales, true)) {
            return $langHeader;
        }

        return $this->normalizeLocale(
            $request->header('Accept-Language')
            ?? $request->query('lang')
            ?? session('locale')
            ?? config('app.fallback_locale', 'en')
        );
    }

    private function normalizeLocale(mixed $locale): string
    {
        return substr(strtolower(trim((string) $locale)), 0, 2);
    }
}
