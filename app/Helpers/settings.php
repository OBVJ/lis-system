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
