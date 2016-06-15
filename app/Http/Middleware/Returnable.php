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
        $request->session()->put('returnTo', $request->url());
        return $next($request);
    }
}
