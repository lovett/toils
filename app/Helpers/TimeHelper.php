<?php

namespace App\Helpers;

class TimeHelper {
    public static function hoursAndMinutes($minutes, $format='%d hours, %d minutes') {
        $hours = intval($minutes / 60);
        $minutes = $minutes % 60;
        return sprintf($format, $hours, $minutes);
    }

    public static function minutesToHours($minutes, $precision=2) {
        return round($minutes / 60, $precision);
    }
}
