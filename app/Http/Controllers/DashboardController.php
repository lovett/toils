<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Project;

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
     * @return Response
     */
    public function index(Request $request)
    {
        $baseQuery = $request->user()->projects();

        $projects = Project::listing($baseQuery);

        $activeProjects = $projects->active()->newest()->get();

        $unfinishedTime = $request->user()->time()->unfinished(5)->get();

        $viewVars = [
            'pageTitle' => 'Dashboard',
            'activeProjects' => $activeProjects,
            'unfinishedTime' => $unfinishedTime,
            'totalUnbilled' => $activeProjects->sum('unbilledTime'),
        ];

        return view('dashboard', $viewVars);
    }
}
