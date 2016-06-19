<?php

namespace App\Http\Middleware;

use Closure;

class Returnable
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

        $request->session()->put('returnTo', $request->url());
        return $response;
    }
}
