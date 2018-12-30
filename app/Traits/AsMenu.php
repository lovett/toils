<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait AsMenu
{


    /**
     * Return a list of key-value pairs suitable for display in an HTML menu.
     *
     * @param Builder         $query     Determines the list being returned
     * @param string          $key       The field name in query to use as the key
     * @param string|string[] $value     The field name in query to use as the value
     * @param string          $separator The separator in a multi-field value comprised of multiple fields
     *
     * @return array
     */
    protected function asMenu(Builder $query, string $key = 'id', $value = 'name', string $separator = ' ')
    {
        $items = $query->get()->reduce(
            function ($acc, $item) use ($key, $value, $separator) {
                if (is_string($value)) {
                    // Value refers to a single field on $item.
                    $acc[$item->$key] = $item->$value;

                    return $acc;
                }

                // Value refers to multiple fields.
                $itemArray = $item->toArray();
                $multiVal  = array_map(function ($field) use ($itemArray) {
                    return array_get($itemArray, $field);
                }, $value);

                $acc[$item->$key] = implode($separator, $multiVal);

                return $acc;
            },
            ['' => '']
        );

        return $items;
    }
}
