<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang', $request->session()->get('locale', config('app.locale', 'ar')));

        if (! in_array($locale, ['ar', 'en'], true)) {
            $locale = 'ar';
        }

        $request->session()->put('locale', $locale);
        App::setLocale($locale);

        $isRtl = $locale === 'ar';
        View::share('currentLocale', $locale);
        View::share('isRtl', $isRtl);

        return $next($request);
    }
}
