<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Custom parent controller class
 *
 * For functionality common to all controllers.
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Standard value for indicating update success via flash
     *
     * @param integer $count The number of updated records.
     *
     * @return array
     */
    protected function userMessageForAffectedRows($count)
    {
        if ($count === 0) {
            return [
                'warning',
                'Nothing updateable was found',
            ];
        }

        return [
            'success',
            'Updated successfully',
        ];
    }

    /**
     * Standard value for indicating creation success via flash
     *
     * @param string $recordType A human-readable label for the created record.
     *
     * @return array
     */
    protected function successMessage($recordType)
    {
        return [
            'success',
            ucfirst($recordType) . ' created successfully!',
        ];
    }

    /**
     * Apply search terms to a query
     *
     * @param Relation $relation The relation being searched.
     * @param string   $query    The search terms as provided by the request.
     *
     * @return array
     */
    protected function parseSearchQuery(Relation $relation, $query)
    {
        if (strpos($query, ':') === false) {
            // No fields were specified, so treat the first one as a default.
            $defaultField = current(array_keys($this->searchFields));

            $query = sprintf('%s:%s', $defaultField, $query);
        }

        // Split by field.
        $fields = array_map(
            function ($field) {
                return explode(':', $field);
            },
            explode(' ', $query)
        );

        // Flatten.
        $fields = array_reduce(
            $fields,
            function ($accumulator, $pair) {
                list($field, $value) = $pair;

                if (is_numeric($value)) {
                    $value = (int) $value;
                }

                // Skip unsupported fields.
                if (array_key_exists($field, $this->searchFields) === false) {
                    return $accumulator;
                }

                $queryField = $this->searchFields[$field];
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

        array_walk(
            $fields,
            function ($values, $field) use ($relation) {
                $relation->where($field, 'LIKE', array_shift($values));

                if (count($values) === 0) {
                    return;
                }

                $relation->where(
                    function ($query) use ($field, $values) {
                        array_walk(
                            $values,
                            function ($value) use ($query) {
                                $query->orWhere($field, 'LIKE', $value);
                            }
                        );
                    }
                );
            }
        );

        return $relation;
    }
}
