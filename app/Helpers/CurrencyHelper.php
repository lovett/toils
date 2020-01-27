<?php
namespace App\Helpers;

use Locale;
use NumberFormatter;

/**
 * Helper functions for displaying currency values.
 */
class CurrencyHelper
{


    /**
     * Format a monetary value with cents
     *
     * @param float|null $value The value to format.
     *
     * @return string
     */
    public static function money(?float $value)
    {
        if ($value === null) {
            return '';
        }

        $fmt = new NumberFormatter(
            Locale::getDefault(),
            NumberFormatter::CURRENCY
        );

        return $fmt->formatCurrency($value, 'USD');
    }

    /**
     * Format a monetary value without cents
     *
     * @param float|null $value The value to format.
     *
     * @return string
     */
    public static function dollars(?float $value)
    {
        if ($value === null) {
            return '';
        }

        $fmt = new NumberFormatter(
            Locale::getDefault(),
            NumberFormatter::CURRENCY
        );

        $fmt->setAttribute(NumberFormatter::FRACTION_DIGITS, 0);
        return $fmt->formatCurrency($value, 'USD');
    }
}
