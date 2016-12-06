<?php

namespace App\Helpers;

/**
 * Helper functions for displaying currency values.
 */
class CurrencyHelper
{

    /**
     * Format a monetary value with cents and currency symbol.
     *
     * @param float $value The value to format.
     *
     * @return string
     */
    public static function withSymbol($value, $symbol='$')
    {
        return $symbol . money_format('%.2n', $value);
    }

    /**
     * Format a monetary value as a whole number.
     *
     * @param float $value The value to format.
     *
     * @return string
     */
    public static function wholeNumberWithSymbol($value, $symbol='$')
    {
        return $symbol . money_format('%.0n', $value);
    }
}
