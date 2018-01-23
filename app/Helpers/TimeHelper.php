<?php

namespace App\Helpers;

use Carbon\Carbon;
use DatePeriod;
use DateInterval;
use DateTime;

/**
 * Helper functions for displaying time values.
 */
class TimeHelper
{

    public static $dateFormat = 'Y-m-d';

    /**
     * Display an integer as an "x hours, y minutes" English phrase.
     *
     * @param int $minutes The value to be converted for display
     *
     * @return string
     */
    public static function hoursAndMinutes($minutes)
    {
        if ($minutes === 0) {
            return '0 minutes';
        }

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

        if (empty($out) || $minutes > 0) {
            $out[] = sprintf($format, $minutes, $minutesLabel);
        }

        return implode(', ', $out);
    }

    /**
     * Convert minutes to hours with rounding.
     *
     * @param int $minutes The number to be divided
     * @param int $precision How many decimal places
     *
     * @return int
     */
    public static function minutesToHours($minutes, $precision = 2)
    {
        return round(($minutes / 60), $precision);
    }

    /**
     * Convert a datetime string to a Carbon instance.
     *
     * For situations where you have a date from a custom query
     * that wasn't automatically converted by Eloquent.
     *
     * @param string $value The raw datetime string
     * @param string $default The value to return in case of nulls
     *
     * @return Carbon;
     */
    public static function dateFromRaw($value, $default = '')
    {
        if (empty($value)) {
            return $default;
        }

        $value = Carbon::parse($value);

        return self::date($value);
    }

    /**
     * Format a date textually: such as Jan 1 2018
     *
     * @param Carbon|null $value The value to format
     *
     * @return string
     */
    public static function readableShortDate(Carbon $value = null)
    {
        if (is_null($value)) {
            return '';
        }

        return $value->format('M n Y');
    }

    /**
     * Format a date numerically: 2018-01-01
     *
     * @param Carbon|null $value The value to format
     *
     * @return string
     */
    public static function date(Carbon $value = null)
    {
        if (is_null($value)) {
            return '';
        }

        return $value->format(self::$dateFormat);
    }

    public static function longDate(Carbon $value = null)
    {
        if (is_null($value)) {
            return '';
        }

        return $value->format('F jS, Y');
    }

    /**
     * Format a time in 12-hour format: 1:00 AM
     *
     * @param Carbon|null $value The value to format
     *
     * @return string
     */
    public static function time(Carbon $value = null)
    {
        if (is_null($value)) {
            return '';
        }

        return $value->format('g:i A');
    }

    public static function roundToNearestMinuteMultiple(Carbon $value, int $multiple)
    {
        $value->minute = self::roundToNearestMultiple($value->minute, $multiple);
        $value->second = 0;

        return $value;
    }

    public static function roundToNearestMultiple(int $value, int $multiple)
    {
        return floor($value / $multiple) * $multiple;
    }

}
