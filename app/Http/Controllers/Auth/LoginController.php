<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;

/**
 * Standard Laravel controller for user login
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        view()->share('pageTitle', 'Log In');
    }

    /**
     * Capture the authenticated user's timezone if available.
     *
     * This overrides an empty method in the AuthenticatesUsers trait.
     * Not returning a value allows the trait to take care of building
     * the response; this override is more like an additional step
     * along the way.
     *
     * @param Request $request The current request.
     * @param User    $user    The authenticated user.
     *
     * @return void
     */
    protected function authenticated(Request $request, User $user)
    {
        if ($request->cookie('TIMEZONE')) {
            $user->setAttribute('timezone', $request->cookie('TIMEZONE'));
            $user->save();
        }
    }
}
