<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Redirect to the url previously captured by the returnable middleware
 */
class BackTo
{


    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request.
     * @param Closure $next    The handler to receive the request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $next($request);

        if ($request->session()->has('returnTo')) {
            return redirect($request->session()->pull('returnTo'));
        }
        abort(500);
    }
}
