<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Standard Laravel controller class
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Apply search terms to a query.
     *
     * @param string|null $query       The search terms provided by the request
     * @param array       $searchables A searchables array from a model
     * @param array       $aliases     Replacement values to map to incoming search terms
     *
     * @return array
     */
    protected function parseSearchQuery($query = null, array $searchables = [], array $aliases = [])
    {
        if (empty($query)) {
            return [];
        }

        $query = strtolower($query);
        $query = filter_var($query, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $query = str_replace(':', ': ', $query);

        $terms = array_fill_keys(array_values($searchables), []);

        // The first searchable field is used as the default, in case one
        // isn't specified in the query.
        $field = current(array_values($searchables));

        $index = 0;

        foreach (preg_split('/\s+/', $query) as $word) {
            if (strpos($word, ':') !== false) {
                $word = rtrim($word, ':');

                if (array_key_exists($word, $searchables)) {
                    $field = $searchables[$word];
                    $index = count($terms[$field]);
                    continue;
                }
            }

            if (empty($terms[$field][$index])) {
                $terms[$field][$index] = '';
            }

            if (array_key_exists($field, $aliases)
                && array_key_exists($word, $aliases[$field])
            ) {
                $word = $aliases[$field][$word];
            }


            $terms[$field][$index] = trim($terms[$field][$index] . ' ' . $word);
        }

        return $terms;
    }
}
