<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Apply search terms to a query.
     *
     * @param string|null $query       The search terms provided by the request
     * @param array       $searchables A searchables array from a model
     *
     * @return array
     */
    protected function parseSearchQuery($query = null, array $searchables = [])
    {
        if (empty($query)) {
            return [];
        }

        $query = strtolower($query);
        $query = filter_var($query, FILTER_SANITIZE_STRING);

        if (strpos($query, ':') === false) {
            // No fields were specified, so treat the first one as a default.
            $defaultField = current(array_keys($searchables));

            $query = sprintf('%s:%s', $defaultField, $query);
        }

        // Split by field.
        $fields = array_map(function ($field) {
            return explode(':', $field);
        }, explode(' ', $query));

        // Flatten.
        $fields = array_reduce(
            $fields,
            function ($accumulator, $pair) use ($searchables) {
                list($field, $value) = $pair;

                if (is_numeric($value)) {
                    $value = (int) $value;
                }

                // Skip unsupported fields.
                if (array_key_exists($field, $searchables) === false) {
                    return $accumulator;
                }

                $queryField = $searchables[$field];
                if (array_key_exists($queryField, $accumulator) === false) {
                    $accumulator[$queryField] = [];
                }

                // Apply a suffix wildcard to non-wildcard strings.
                if (is_string($value) && strpos($value, '*') === false) {
                    $value .= '*';
                }

                // Convert wildcards to SQL syntax.
                if (strpos($value, '*') !== false) {
                    $value = str_replace('*', '%', $value);
                }

                $accumulator[$queryField][] = $value;

                return $accumulator;
            },
            []
        );

        return $fields;
    }
}
