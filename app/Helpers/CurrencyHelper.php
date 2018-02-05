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
     * @param float $value The value to format.
     *
     * @return string
     */
    public static function money($value)
    {
        return money_format('%.2n', $value);
    }

    /**
     * Calculate an hourly rate
     *
     * @param float $value Total money
     * @param int $minutes Total time
     *
     * @return int Hourly money, rounded
     *
     */
    public static function hourlyRate($amount=0, $minutes=0)
    {
        if ($minutes < 1) {
            return '';
        }

        $rate = $amount / $minutes * 60;

        return money_format('%.0n', $rate);
    }
}
