<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $sessionLocale = Session::get('locale');
        $cookieLocale = Cookie::get('locale');
        $appLocale = app()->getLocale();

        $locale = $sessionLocale ?: $cookieLocale;

        if ($locale && in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
            Log::info('SetLocale middleware: Set locale to: ' . $locale . ' (from ' . ($sessionLocale ? 'session' : 'cookie') . '), App locale was: ' . $appLocale . ', now: ' . app()->getLocale());
        } else {
            Log::info('SetLocale middleware: No valid locale found. Session: ' . $sessionLocale . ', Cookie: ' . $cookieLocale . ', current app locale: ' . $appLocale);
        }

        return $next($request);
    }
}
