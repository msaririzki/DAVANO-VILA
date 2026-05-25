<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('locales.supported'));
        $fallback = config('locales.fallback', 'id');

        if ($request->has('lang')) {
            $locale = $this->normalizeLocale((string) $request->query('lang'), $supported, $fallback);
            $request->session()->put('locale', $locale);
            App::setLocale($locale);

            return $this->redirectWithoutLang($request);
        }

        $locale = $request->session()->get('locale');

        if (! in_array($locale, $supported, true)) {
            $locale = $this->preferredLocale($request, $supported, $fallback);
            $request->session()->put('locale', $locale);
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * @param  array<int, string>  $supported
     */
    private function preferredLocale(Request $request, array $supported, string $fallback): string
    {
        $accepted = $request->getLanguages();

        foreach ($accepted as $language) {
            $locale = $this->normalizeLocale($language, $supported);

            if ($locale !== null) {
                return $locale;
            }
        }

        return $fallback;
    }

    /**
     * @param  array<int, string>  $supported
     */
    private function normalizeLocale(string $locale, array $supported, ?string $fallback = null): ?string
    {
        $locale = strtolower(str_replace('_', '-', $locale));
        $locale = explode('-', $locale)[0] ?: $fallback;

        return in_array($locale, $supported, true) ? $locale : $fallback;
    }

    private function redirectWithoutLang(Request $request): RedirectResponse
    {
        $query = $request->query();
        unset($query['lang']);

        $url = $request->url();

        if ($query !== []) {
            $url .= '?'.http_build_query($query);
        }

        return redirect()->to($url);
    }
}
