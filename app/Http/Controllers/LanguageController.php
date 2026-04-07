<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        Log::info('Language switch called with locale: ' . $locale);

        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale);
            Session::save(); // Force save the session
            Log::info('Locale set in session: ' . $locale . ', Session ID: ' . Session::getId());

            // Also set a cookie as backup
            cookie('locale', $locale, 60*24*30); // 30 days
        } else {
            Log::info('Invalid locale: ' . $locale);
        }

        return redirect()->back()->with('message', 'Language switched to ' . $locale);
    }
}
