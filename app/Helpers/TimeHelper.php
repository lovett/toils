<?php

namespace App\Helpers;

class TimeHelper {
    public static function hoursAndMinutes($minutes) {
        $hours = intval($minutes / 60);
        $minutes = $minutes % 60;
        $hoursLabel = ($hours === 1)? 'hour' : 'hours';
        $minutesLabel = ($minutes === 1)? 'minute' : 'minutes';
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

    public static function minutesToHours($minutes, $precision=2) {
        return round($minutes / 60, $precision);
    }
}
