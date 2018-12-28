<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * Standard Laravel middleware class.
 */
class RedirectIfAuthenticated
{


    /**
     * Handle an incoming request.
     *
     * @param Request     $request The incoming request.
     * @param Closure     $next    The next middleware handler.
     * @param string|null $guard   Optional guard instance.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
