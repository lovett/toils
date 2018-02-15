<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Search
{
    /**
     * Apply a list of filters to an SQL query.
     *
     * @param Builder $query  The query to apply the search to
     * @param array   $fields The fields and terms to apply to the query
     *
     * @return self
     */
    public static function search(Builder $query, array $fields = [])
    {
        array_walk(
            $fields,
            function ($values, $field) use ($query) {

                $query->where($field, 'LIKE', sprintf('%%%s%%', array_shift($values)));

                if (count($values) === 0) {
                    return $query;
                }

                $query->where(
                    function ($query) use ($field, $values) {
                        array_walk(
                            $values,
                            function ($value) use ($field, $query) {
                                $query->orWhere($field, 'LIKE', sprintf('%%%s%%', $value));
                            }
                        );
                    }
                );
            }
        );

        return $query;
    }
}
