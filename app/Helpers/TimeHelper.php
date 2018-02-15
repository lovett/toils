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
     * @param int $minutes The value to be displayed
     *
     * @return string
     */
    public static function hoursAndMinutes($minutes)
    {
        if ($minutes === 0) {
            return '0 minutes';
        }

        $hours = intval($minutes / 60);
        $minutes = $minutes % 60;

        $format = '%d %s';

        $out = [];

        if ($hours > 0) {
            $label = ($hours === 1)? 'hour' : 'hours';
            $out[] = sprintf($format, $hours, $label);
        }

        if (empty($out) || $minutes > 0) {
            $label = ($minutes === 1)? 'minute' : 'minutes';
            $out[] = sprintf($format, $minutes, $label);
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
