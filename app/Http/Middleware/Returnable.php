<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Closure;

/**
 * Capture the current url to the sesion for future return
 */
class Returnable
{


    /**
     * Handle an incoming request.
     *
     * @param Request $request The curent request.
     * @param Closure $next    The handler to receive the request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $request->session()->put('returnTo', $request->url());
        return $response;
    }
}
