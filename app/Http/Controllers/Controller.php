<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function userMessageForAffectedRows($count)
    {
        if ($count == 0) {
            return ['warning', 'Nothing updateable was found'];
        }

        return ['success', 'Updated successfully'];
    }


}
