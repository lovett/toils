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
     * @param int $minutes   The number to be divided
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
     * @param string $value   The raw datetime string
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
     * Default application formatter for dates.
     *
     * @param Carbon|null $value The value to format
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

    public static function dateField(Carbon $value = null)
    {
        if (empty($value)) {
            return '';
        }

        return $value->format('Y-m-d');
    }

    public static function timeField(Carbon $value = null)
    {
        if (empty($valu)) {
            return '';
        }

        return $value->format('g:i A');
    }


    public static function ranges() {
        return [
            'today' => new DateTime(),

            'yesterday' => new DateTime('-1 day'),

            '2-days-ago' => new DateTime('-2 day'),

            '3-days-ago' => new DateTime('-3 day'),

            'month' => new DatePeriod(
                new DateTime('Jan 1'),
                new DateInterval('P1M'),
                new DateTime('Dec 31')
            ),

            'day' => new DatePeriod(
                new DateTime('Jan 1'),
                new DateInterval('P1D'),
                new DateTime('Feb 1')
            ),

            'year' => new DatePeriod(
                new DateTime('-5 years'),
                new DateInterval('P1Y'),
                new DateTime('first day of next year')
            ),

            'hour' => new DatePeriod(
                new DateTime('Jan 1 1:00'),
                new DateInterval('PT1H'),
                new DateTime('Jan 1 13:00')
            ),

            'minute' => new DatePeriod(
                new DateTime('Jan 1 00:00'),
                new DateInterval('PT5M'),
                new DateTime('Jan 1 01:00')
            ),
        ];
    }
}
