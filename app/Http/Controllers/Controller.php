<?php

namespace App\Http\Controllers;

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
}
