<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

/**
 * Standard Laravel middleware class.
 */
class Authenticate extends Middleware
{


    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request The incoming request

     * @return string
     */
    protected function redirectTo(Request $request)
    {
        if ($request->expectsJson() === false) {
            return route('login');
        }
    }
}
