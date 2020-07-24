<?php

namespace App\Http\Middleware;

use \Illuminate\Http\Request;
use Closure;
use Illuminate\View\View;

/**
 * Make the user's timezone available to all views.
 */
class ShareTimezone
{


    /**
     * Make the user's timezone avaialble to all views.
     *
     * @param Request $request The incoming request.
     * @param Closure $next    The next middleware handler.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user) {
            view()->share('timezone', $user->timezone);
        }

        return $next($request);
    }
}
