<?php

namespace App\Helpers;

class AddressHelper {
    public static function mailingAddress($record) {

        $out = '';

        $appendIfSet = function ($key, $out, $prefix='', $suffix='') use ($record) {
            if (!empty($record->$key)) {
                $out .= $prefix . $record->$key . $suffix;
            }
            return $out;
        };

        $out = $appendIfSet('address1', $out, null, "\n");
        $out = $appendIfSet('address2', $out, null, "\n");
        $out = $appendIfSet('city', $out, null, empty($record->locality)? null : ', ');
        $out = $appendIfSet('locality', $out, null, ' ');
        $out = $appendIfSet('postal_code', $out);

        return $out;
    }

    public static function phoneUrl($value)
    {
        if (empty($value)) return '';

        $plainValue = preg_replace('/[^0-9x\#*]/', null, $value);
        $formattedValue = phone_format($plainValue, 'US');
        return sprintf('<a href="tel://%s">%s</a>', $plainValue, $formattedValue);

    }
}
