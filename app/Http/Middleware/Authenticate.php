<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * Standard Laravel Authenticate middleware
 */
class Authenticate
{

    /**
     * The Guard implementation.
     *
     * @var Guard Guard instance.
     */
    protected $auth;


    /**
     * Create a new filter instance.
     *
     * @param Guard $auth Guard instance.
     *
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

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
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            }
            return redirect()->guest('auth/login');
        }

        return $next($request);
    }
}
