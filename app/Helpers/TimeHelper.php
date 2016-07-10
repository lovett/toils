<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Helper functions for displaying time values
 */
class TimeHelper
{


    /**
     * Display an integer as an "x hours, y minutes" English phrase
     *
     * @param integer $minutes The value to be converted for display.
     *
     * @return string
     */
    public static function hoursAndMinutes($minutes)
    {
        $hours = intval($minutes / 60);

        $minutes = ($minutes % 60);

        $hoursLabel = 'hours';
        if ($hours === 1) {
            $hoursLabel = 'hours';
        }

        $minutesLabel = 'minutes';
        if ($minutes === 1) {
            $minutesLabel = 'minute';
        }

        $format = '%d %s';

        $out = [];

        if ($hours > 0) {
            $out[] = sprintf($format, $hours, $hoursLabel);
        }

        if ($minutes > 0) {
            $out[] = sprintf($format, $minutes, $minutesLabel);
        }

        return implode(', ', $out);
    }

    /**
     * Convert minutes to hours with rounding
     *
     * @param integer $minutes   The number to be divided.
     * @param integer $precision How many decimal places.
     *
     * @return integer
     */
    public static function minutesToHours($minutes, $precision = 2)
    {
        return round(($minutes / 60), $precision);
    }

    /**
     * Convert a datetime string to a Carbon instance
     *
     * For situations where you have a date from a custom query
     * that wasn't automatically converted by Eloquent.
     *
     * @param string $value The raw datetime string.
     *
     * @return Carbon;
     */
    public static function dateFromRaw($value)
    {
        if (empty($value)) {
            return '';
        }

        $value = Carbon::parse($value);
        return self::date($value);
    }

    /**
     * Default application formatter for dates
     *
     * @param Carbon|null $value The value to format.
     *
     * @return string
     */
    public static function date(Carbon $value)
    {
        if (empty($value)) {
            return '';
        }

        return $value->format('M n Y');
    }
}
