<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * Controller for the user homepage.
 */
class DashboardController extends Controller
{
    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('appSection', 'dashboard');
        view()->share('page_title', 'Dashboard');
    }

    /**
     * Display the dashboard.
     *
     * @return Response
     */
    public function index()
    {
        return view('dashboard.show');
    }
}
