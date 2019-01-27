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


    /**
     * Calculate an hourly rate
     *
     * @param float $money   Total money.
     * @param int   $minutes Total time.
     *
     * @return string Hourly money, rounded
     */
    public static function hourlyRate(float $money = 0, int $minutes = 0)
    {
        if ($minutes < 1) {
            return '';
        }

        $rate = $money / $minutes * 60;

        return money_format('%.0n', $rate);
    }
}
