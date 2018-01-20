<?php

namespace App\Helpers;

use Exception;

/**
 * Helper functions for displaying addresses.
 */
class AddressHelper
{
    /**
     * Display a mailing address, taking care to account for missing fields.
     *
     * @param string $record An object containing address fields
     *
     * @return string
     */
    public static function mailingAddress($record)
    {
        $out = '';

        $appendIfSet = function (
            $key,
            $out,
            $prefix = '',
            $suffix = ''
        ) use ($record) {
            if (empty($record->$key) === false) {
                $out .= $prefix.$record->$key.$suffix;
            }

            return $out;
        };

        $punctuationAfterLocality = ', ';
        if (empty($record->locality)) {
            $punctuationAfterLocality = '';
        }

        $out = $appendIfSet('address1', $out, null, "\n");
        $out = $appendIfSet('address2', $out, null, "\n");
        $out = $appendIfSet('city', $out, null, $punctuationAfterLocality);
        $out = $appendIfSet('locality', $out, null, ' ');
        $out = $appendIfSet('postalCode', $out);

        return $out;
    }

    /**
     * Display a phone number as a hyperlink with the tel protocol.
     *
     * @param string $value The number to display
     *
     * @return string
     */
    public static function phoneUrl($value)
    {
        if (empty($value)) {
            return '';
        }

        $plainValue = preg_replace('/[^0-9x\#*]/', null, $value);

        try {
            $formattedValue = phone($plainValue, 'US')->formatInternational();
        } catch (Exception $e) {
            $formattedValue = $plainValue;
        }

        return sprintf(
            '<a href="tel://%s">%s</a>',
            $plainValue,
            $formattedValue
        );
    }
}
