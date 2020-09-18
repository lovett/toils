<?php

namespace App\Helpers;

use Exception;
use App\Models\User;
use App\Models\Client;

/**
 * Helper functions for displaying addresses.
 */
class AddressHelper
{


    /**
     * Display the mailing address for a user.
     *
     * @param User $user A user instance.
     */
    public static function userMailingAddress(User $user)
    {
        return static::mailingAddress($user, [
            'displayName',
            'address1',
            'address2',
            'city',
            'locality',
            'postalCode',
            [
                'Tel',
                'phone',
            ],
            'email',
        ]);
    }


    /**
     * Display the mailing address for a client.
     *
     * @param Client $client A client instance.
     */
    public static function clientMailingAddress(Client $client)
    {
        return static::mailingAddress($client, [
            'name',
            [
                'c/o',
                'contactName',
            ],
            'address1',
            'address2',
            'city',
            'locality',
            'postalCode',
        ]);
    }


    /**
     * Display the contact address for a client.
     *
     * @param Client $client A client instance.
     */
    public static function clientContact(Client $client)
    {
        return static::mailingAddress($client, [
            'contactName',
            'address1',
            'address2',
            'city',
            'locality',
            'postalCode',
        ]);
    }


    /**
     * Display a mailing address, taking care to account for missing fields.
     *
     * @param User|Client $record The data object.
     * @param Array       $fields A list of properties to extract from the data object.
     *
     * @return string
     */
    private static function mailingAddress($record, array $fields = [])
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

            if (empty($record->$key) === false) {
                $acc .= ltrim(sprintf('%s %s%s', $prefix, $record->$key, $suffix));
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
    public static function phoneUrl(string $value)
    {
        if (empty($value)) {
            return '';
        }

        $plainValue = preg_replace('/[^0-9x\#*]/', '', $value);

        $matches = [];
        $matchResult = preg_match(
            '/(1)?(\d{3})?(\d{3})(\d{4})(.*)?/',
            $plainValue,
            $matches
        );

        if ($matchResult !== 1) {
            return $value;
        }

        $formattedValue = '';
        if (empty($matches[1]) === false) {
            $formattedValue .= sprintf('+%s ', $matches[1]);
        }

        if (empty($matches[2]) === false) {
            $formattedValue .= sprintf('(%s) ', $matches[2]);
        }

        $formattedValue .= sprintf('%s-%s ', $matches[3], $matches[4]);

        if (empty($matches[5]) === false) {
            $formattedValue .= $matches[5];
        }

        return sprintf(
            '<a href="tel://%s">%s</a>',
            $plainValue,
            $formattedValue
        );
    }
}
