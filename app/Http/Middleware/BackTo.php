<?php

namespace App\Http\Middleware;

use Closure;

class BackTo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($request->session()->has('returnTo')) {
            return redirect($request->session()->pull('returnTo'));
        }
        abort(500);
    }
}
