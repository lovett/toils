<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\Client;
use App\Time;

/**
 * Resource controller for projects.
 */
class ProjectController extends Controller
{
    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('appSection', 'projects');
    }

    /**
     * Display a list of projects.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('q');

        $baseQuery = $request->user()->projects();

        $projects = Project::listing($baseQuery);

        if ($search !== null) {
            $searchFields = $this->parseSearchQuery(
                $search,
                Project::$searchables
            );

            $projects = Project::search($projects, $searchFields);
        }

        $projects = $projects->simplePaginate(15);

        $viewVars = [
            'page_title' => 'Projects',
            'projects' => $projects,
            'search' => $search,
            'searchFields' => array_keys(Project::$searchables),
        ];

        return view('projects.list', $viewVars);
    }

    /**
     * Show the form for creating a new project.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $clients = $request->user()->clientsForMenu();

        $clientId = $request->input('client', null);
        if (array_key_exists($clientId, $clients) === false) {
            $clientId = null;
        }

        $model = new Project(
            [
                'active' => true,
                'billable' => true,
                'client_id' => $clientId,
            ]
        );

        $viewVars = [
            'page_title' => 'Add a project',
            'model' => $model,
            'clients' => $clients,
            'submission_route' => 'project.store',
            'submission_method' => 'POST',
            'app_section' => 'project',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('projects.form', $viewVars);
    }

    /**
     * Save a new project to the database.
     *
     * @param ProjectRequest $request The incoming request
     */
    public function store(ProjectRequest $request)
    {
        $project = new Project();

        $clientId = $request->input('client_id', 0);

        $client = $request->user()->clients()->findOrFail($clientId);

        $project->name = $request->input('name');
        $project->active = $request->input('active', 0);
        $project->billable = $request->input('billable', 0);
        $project->taxDeducted = $request->input('taxDeducted', 0);

        $project->user()->associate($request->user());
        $project->client()->associate($client);
        $project->save();
    }

    /**
     * Display a project.
     *
     * @param Request $request The incoming request
     * @param int     $id      A project primary key
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
     * Show the form for editing a project.
     *
     * @param Request $request The incoming request
     * @param int     $id      A project primary key
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
     * Update an existing project.
     *
     * @param ProjectRequest $request The incoming request
     * @param int            $id      A project primary key
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
     * Delete a project.
     *
     * Projects use soft deletion.
     *
     * @param Request $request The incoming request
     * @param int     $id      A project primary key
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
