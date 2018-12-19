<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Project;
use App\Time;

/**
 * Controller class for the logged-in homepage of the site.
 */
class DashboardController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('module', 'dashboard');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request The incoming request
     */
    public function index(Request $request)
    {
        $baseQuery = $request->user()->projects();

        $projects = Project::listing($baseQuery);

        $activeProjects = $projects->active()->newest()->get();

        $unfinishedTimeQuery = $request->user()->time()->unfinished()->getQuery();
        $unfinishedTime = Time::listing($unfinishedTimeQuery)->get();

        $viewVars = [
            'pageTitle' => 'Dashboard',
            'activeProjects' => $activeProjects,
            'unfinishedTime' => $unfinishedTime,
            'totalUnbilled' => $activeProjects->sum('unbilledTime'),
        ];

        return view('dashboard', $viewVars);
    }
}
