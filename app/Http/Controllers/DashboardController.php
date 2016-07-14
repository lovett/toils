<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;

/**
 * Controller for the user homepage
 */
class DashboardController extends Controller
{


    /**
     * Set middleware and shared view values
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('app_section', 'dashboard');
        view()->share('page_title', 'Dashboard');
    }


    /**
     * Display the dashboard
     *
     * @return Response
     */
    public function index()
    {
        return view('dashboard.show');
    }
}
