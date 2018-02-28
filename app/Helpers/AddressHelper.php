<?php

namespace App\Helpers;

use Exception;

/**
 * Helper functions for displaying addresses.
 */
class AddressHelper
{

    public static function userMailingAddress($record)
    {
        $fields = ['displayName', 'address1', 'address2', 'city', 'locality', 'postalCode', ['Tel', 'phone'], 'email'];
        return static::mailingAddress($record, $fields);
    }

    public static function clientMailingAddress($record)
    {
        $fields = ['name', ['c/o', 'contactName'], 'address1', 'address2', 'city', 'locality', 'postalCode'];
        return static::mailingAddress($record, $fields);
    }

    public static function clientContact($record)
    {
        $fields = [['c/o', 'contactName'], 'address1', 'address2', 'city', 'locality', 'postalCode'];
        return static::mailingAddress($record, $fields);
    }


    /**
     * Display a mailing address, taking care to account for missing fields.
     *
     * @param string $record An object containing address fields
     *
     * @return string
     */
    private static function mailingAddress($record, $fields=[])
    {
        $address = array_reduce($fields, function ($acc, $field) use ($record) {
            $prefix = '';
            $key = $field;
            if (is_array($field)) {
                list($prefix, $key) = $field;
            }

            $suffix = "\n";
            if ($key === 'city') {
                $suffix = ', ';
            }

            if ($key === 'locality') {
                $suffix = ' ';
            }

            if (!empty($record->$key)) {
                $acc .= ltrim(sprintf("%s %s%s", $prefix, $record->$key, $suffix));
            }
            return $acc;
        }, '');

        return $address;
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

        $matchResult = preg_match("/(1)?(\d{3})?(\d{3})(\d{4})(.*)?/", $plainValue, $matches);

        if ($matchResult !== 1) {
            return $value;
        }

        $formattedValue = '';
        if (!empty($matches[1])) {
            $formattedValue .= sprintf('+%s ', $matches[1]);
        }

        if (!empty($matches[2])) {
            $formattedValue .= sprintf('(%s) ', $matches[2]);
        }

        $formattedValue .= sprintf('%s-%s ', $matches[3], $matches[4]);

        if (!empty($matches[5])) {
            $formattedValue .= $matches[5];
        }

        return sprintf(
            '<a href="tel://%s">%s</a>',
            $plainValue,
            $formattedValue
        );
    }
}
