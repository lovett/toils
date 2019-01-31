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
        foreach ($fields as $name => $values) {
            if (empty($values)) {
                continue;
            }

            $query->where(function ($query) use ($name, $values) {
                foreach ($values as $value) {
                    if (is_numeric($value)) {
                        $query->orWhere($name, '=', (int) $value);
                    }

                    $wildValue = '%' . $value . '%';
                    $query->orWhere($name, 'LIKE', $wildValue);
                }
            });
        }

        return $query;
    }
}
