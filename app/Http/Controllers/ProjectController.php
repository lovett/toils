<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Requests\ProjectRequest;
use App\Http\Controllers\Controller;
use App\Project;
use App\Client;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a list of projects
     *
     * @return Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Show the form for creating a new project
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $project = new Project();
        $project->active = true;
        $project->billable = true;
        if ($request->has('client')) {
            $project->client_id = $request->input('client');
        }

        $clientId = $request->input('client');
        $viewVars = [
            'page_title' => 'Add a project',
            'model' => $project,
            'clients' => $request->user()->clientsForMenu(),
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
     * @return Response
     */
    public function store(ProjectRequest $request)
    {
        $project = new Project;
        $client = Client::find($request->client_id);

        $project->name = $request->input('name');
        $project->active = $request->input('active', 0);
        $project->billable = $request->input('billable', 0);
        $project->tax_deducted = $request->input('tax_deducted', 0);
        $project->user()->associate($request->user());
        $project->client()->associate($client);
        $project->save();

        return redirect()->route('client.show', ['client' => $project->client]);
    }

    /**
     * Display a project
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing a project
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $project = Project::find($id);

        $viewVars = [
            'page_title' => 'Edit project',
            'model' => $project,
            'clients' => $request->user()->clientsForMenu(),
            'submission_route' => ['project.update', $project->id],
            'submission_method' => 'PUT',
            'app_section' => 'project',
        ];

        return view('projects.form', $viewVars);

    }

    /**
     * Update an existing project
     *
     * @param  ProjectRequest  $request
     * @param  int  $id
     * @return Response
     */
    public function update(ProjectRequest $request, $id)
    {
        $project = Project::find($id);
        $client = Client::find($request->input('client_id'));

        $project->client()->associate($client);

        $project->active = $request->input('active', 0);

        $project->billable = $request->input('billable', 0);

        $project->tax_deducted = $request->input('tax_deducted', 0);

        $project->update($request->all());

        return redirect()->route('client.show', $project->client_id);
    }

    /**
     * Delete a project
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $project = Project::where('id', $id)
                 ->where('user_id', $request->user()->id)
                 ->firstOrFail();

        $project->delete();

        return redirect()->route('client.show', $project->client_id);
    }
}
