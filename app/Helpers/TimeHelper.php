<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

/**
 * Helper functions for displaying time values.
 */
class TimeHelper
{

    /**
     * The default format for representing a date.
     *
     * @var string
     */
    public static $dateFormat = 'Y-m-d';

    /**
     * Display an integer as an "x hours, y minutes" English phrase.
     *
     * @param int $minutes The value to be displayed
     *
     * @return string
     */
    public static function hoursAndMinutes(int $minutes)
    {
        if ($minutes === 0) {
            return '0 minutes';
        }

        $hours = intval($minutes / 60);
        $minutes = $minutes % 60;

        $format = '%d %s';

        $out = [];

        if ($hours > 0) {
            $label = ($hours === 1) ? 'hour' : 'hours';
            $out[] = sprintf($format, $hours, $label);
        }

        if (empty($out) || $minutes > 0) {
            $label = ($minutes === 1) ? 'minute' : 'minutes';
            $out[] = sprintf($format, $minutes, $label);
        }

        return implode(', ', $out);
    }

    /**
     * Convert minutes to hours with rounding.
     *
     * @param int $minutes   The number to be divided
     * @param int $precision How many decimal places
     *
     * @return int
     */
    public static function minutesToHours(int $minutes, int $precision = 2)
    {
        return round(($minutes / 60), $precision);
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
        if ($value === null) {
            return '';
        }

        return self::shiftTimezone($value)->format('M j Y');
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
        if ($value === null) {
            return '';
        }

        return self::shiftTimezone($value)->format(self::$dateFormat);
    }

    /**
     * Convert a Carbon instance to another timezone
     *
     * @param Carbon      $value    The initial Carbon instance, most likely in UTC
     * @param string|null $timezone The timezone to shift to, typically not specified.
     *
     * @return Carbon
     */
    public static function shiftTimezone(Carbon $value, string $timezone = null)
    {
        if ($timezone === null) {
            $timezone = Cookie::get('TIMEZONE', 'UTC');
        }

        return $value->setTimezone($timezone);
    }

    /**
     * Format a date in long form: January 1st, 2018
     *
     * @param Carbon|null $value The value to format
     *
     * @return string
     */
    public static function longDate(Carbon $value = null)
    {
        if ($value === null) {
            return '';
        }

        return self::shiftTimezone($value)->format('F jS, Y');
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
        if ($value === null) {
            return '';
        }

        return self::shiftTimezone($value)->format('g:i A');
    }

    /**
     * Round the minutes of a time value to a given multiple.
     *
     * @param Carbon|null $value    The value to format
     * @param int         $multiple The nearest value to round to.
     *
     * @return string
     */
    public static function roundToNearestMinuteMultiple(Carbon $value, int $multiple)
    {
        $value->minute = self::roundToNearestMultiple($value->minute, $multiple);
        $value->second = 0;

        return $value;
    }

    /**
     * Round a number to a given multiple.
     *
     * @param int $value    The value to round.
     * @param int $multiple The nearest value to round to.
     *
     * @return int
     */
    public static function roundToNearestMultiple(int $value, int $multiple)
    {
        return floor($value / $multiple) * $multiple;
    }

    /**
     * Format a date as a number of days relative to the present.
     *
     * @param Carbon|null $value The value to format.
     *
     * @return string
     */
    public static function daysAgo(Carbon $value = null)
    {
        return self::shiftTimezone($value)->diffForHumans();
    }
}
