<?php

use App\Models\Setting;

if (!function_exists('app_currency')) {
    /**
     * Format an amount with the system's global currency setting.
     * The preference is: {Amount} {CurrencySymbol} (e.g. 500 SDG).
     *
     * @param float|int $amount
     * @return string
     */
    function app_currency($amount)
    {
        // Add cache later if needed for high traffic, but simple DB query is fine for now
        $currencySymbol = Setting::get('currency_symbol', 'SDG');
        
        return number_format((float)$amount, 2) . ' ' . $currencySymbol;
    }
}

if (!function_exists('ar_reshape')) {
    /**
     * Reshape Arabic text for PDF rendering.
     *
     * @param string $text
     * @return string
     */
    function ar_reshape($text)
    {
        if (app()->getLocale() !== 'ar' || empty($text)) {
            return $text;
        }

        try {
            return \App\Helpers\ArabicReshaper::reshape($text);
        } catch (\Throwable $e) {
            return $text;
        }
    }
}
