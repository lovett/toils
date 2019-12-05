<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Closure;

/**
 * Capture the most recent index or show view for redirect after
 * create or update.
 *
 * This makes redirects more natural by bringing you back to where you
 * left off.
 */
class IntendedUrl
{


    /**
     * Store index and show URLs in the session.
     *
     * @param Request $request The incoming request.
     * @param Closure $next    Standard Laravel middleware callback.
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next)
    {
        $route = Route::current();
        $whitelist = ['index', 'show'];
        $blacklist = ['invoice.show'];

        if (in_array($route->getAction(), $blacklist) === false) {
            if (in_array($route->getActionMethod(), $whitelist) === true) {
                $request->session()->put(
                    'url.intended',
                    $request->fullUrl()
                );
            }
        }

        return $next($request);
    }
}
