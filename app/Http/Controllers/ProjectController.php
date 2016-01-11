<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a list of projects
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new project
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $viewVars = [
            'page_title' => 'Add a project',
            'model' => new Project(),
            'clients' => $request->user()->clients()->get(),
            'submission_route' => 'project.store',
            'submission_method' => 'POST',
            'app_section' => 'project',
        ];

        return view('projects.form', $viewVars);

    }

    /**
     * Save a new project to the database
     *
     * @param  ProjectRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display a project
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing a project
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
    }

    /**
     * Update an existing project
     *
     * @param  ProjectRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Delete a project
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
    }
}
