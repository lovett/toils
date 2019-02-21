<?php
namespace App\Helpers;

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

        return money_format('%.2n', $value);
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

        return money_format('%.0n', $value);
    }
}
