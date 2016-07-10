<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Requests\ProjectRequest;
use App\Http\Controllers\Controller;
use App\Project;
use App\Client;
use App\Time;

/**
 * Resource controller for projects
 */
class ProjectController extends Controller
{


    /**
     * Set middleware and shared view values
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('app_section', 'project');
    }

    /**
     * Display a list of projects
     *
     * @param Request $request The incoming request.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = null;

        $projects = Project::listing($request->user()->projects());

        if ($request->get('q')) {
            $search = strtolower($request->get('q'));
            $search = filter_var($search, FILTER_SANITIZE_STRING);
            $projects->where('name', 'like', '%' . $search . '%');
        }

        $projects = $projects->simplePaginate(15);

        $viewVars = [
            'page_title' => 'Projects',
            'projects' => $projects,
            'search' => $search,
            'searchRoute' => 'project.index',
            'searchFields' => [
                'name',
                'client',
                'created',
                'active',
            ],
        ];

        return view('projects.list', $viewVars);
    }

    /**
     * Show the form for creating a new project
     *
     * @param Request $request The incoming request.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $project = new Project();

        $project->active   = true;
        $project->billable = true;

        if ($request->has('client')) {
            $clientId = $request->input('client', 0);

            $client = $request->user()->clients->findOrFail($clientId);

            $project->client()->associate($client);
        }

        $viewVars = [
            'page_title' => 'Add a project',
            'model' => $project,
            'clients' => $request->user()->clientsForMenu(),
            'submission_route' => 'project.store',
            'submission_method' => 'POST',
            'app_section' => 'project',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('projects.form', $viewVars);

    }

    /**
     * Save a new project to the database
     *
     * @param ProjectRequest $request The incoming request.
     *
     * @return void
     */
    public function store(ProjectRequest $request)
    {
        $project = new Project;

        $clientId = $request->input('client_id', 0);

        $client = $request->user()->clients()->findOrFail($clientId);

        $project->name        = $request->input('name');
        $project->active      = $request->input('active', 0);
        $project->billable    = $request->input('billable', 0);
        $project->taxDeducted = $request->input('taxDeducted', 0);

        $project->user()->associate($request->user());
        $project->client()->associate($client);
        $project->save();
    }

    /**
     * Display a project
     *
     * @param Request $request The incoming request.
     * @param integer $id      A project primary key.
     *
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $project = $request->user()->projects()
                 ->with('client')
                 ->findOrFail($id);

        $numMonths = 6;

        $timeByMonth = Time::forProjectAndUserByMonth(
            $project,
            $request->user(),
            $numMonths
        );

        $totalTime = $project->time()->sum('minutes');

        $slice = array_slice($timeByMonth, 0, $numMonths);

        $sliceTotal = array_sum($slice);

        $viewVars = [
            'project' => $project,
            'page_title' => $project->name,
            'totalTime' => $totalTime,
            'slice' => $slice,
            'sliceTotal' => $sliceTotal,
            'sliceRange' => $numMonths,
        ];

        return view('projects.show', $viewVars);

    }

    /**
     * Show the form for editing a project
     *
     * @param Request $request The incoming request.
     * @param integer $id      A project primary key.
     *
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $project = Project::find($id);

        $viewVars = [
            'page_title' => 'Edit project',
            'model' => $project,
            'clients' => $request->user()->clientsForMenu(),
            'submission_route' => [
                'project.update',
                $project->id,
            ],
            'submission_method' => 'PUT',
            'app_section' => 'project',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('projects.form', $viewVars);

    }

    /**
     * Update an existing project
     *
     * @param ProjectRequest $request The incoming request.
     * @param integer        $id      A project primary key.
     *
     * @return Response
     */
    public function update(ProjectRequest $request, $id)
    {
        $project = Project::find($id);

        $affectedRows = $project->update($request->all());

        $userMessage = $this->userMessageForAffectedRows($affectedRows);

        return redirect()->route('project.show', [$project->id])->with(
            'userMessage',
            $userMessage
        );
    }

    /**
     * Delete a project
     *
     * Projects use soft deletion.
     *
     * @param Request $request The incoming request.
     * @param integer $id      A project primary key.
     *
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->projects()
                      ->where('id', $id)
                      ->delete();

        $userMessage = [
            'success',
            'Deleted successfully',
        ];

        if ($affectedRows === 0) {
            $userMessage = [
                'warning',
                'Nothing deletable was found',
            ];
        }

        return redirect()->route('project.index')
            ->with('userMessage', $userMessage);
    }
}
