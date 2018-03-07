<?php

namespace App\Http\Controllers;

use App\Client;
use App\Helpers\MessagingHelper;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\Time;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Resource controller for projects
 */
class ProjectController extends Controller
{
    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('module', 'project');
    }

    /**
     * Display a list of projects
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $baseQuery = $request->user()->projects();

        $projects = Project::listing($baseQuery);

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Project::$searchables
            );

            $projects = Project::search($projects, $searchFields);
        }

        $projects = $projects->simplePaginate(15);

        $viewVars = [
            'collection' => $projects,
            'emptyMessage' => 'There are no projects.',
            'pageTitle' => 'Project List',
            'query' => $query,
            'searchFields' => array_keys(Project::$searchables),
        ];

        return view('list', $viewVars);
    }

    /**
     * Show the form for creating a new project
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
            'pageTitle' => 'Add a project',
            'model' => $model,
            'clients' => $clients,
            'submission_route' => 'project.store',
            'submission_method' => 'POST',
            'app_section' => 'project',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('project.form', $viewVars);
    }

    /**
     * Save a new project to the database
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

        $project->client()->associate($client);
        $project->save();

        MessagingHelper::flashCreated($client->name);

        return redirect()->route(
            'project.show',
            [$project->id]
        );

    }

    /**
     * Display a project
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

        $invoices = $project->invoices()->forList()->newest(5)->get();

        $totalTime = $project->time()->sum('minutes');

        $totalMoney = $project->invoices()->paid()->sum('amount');

        $totalUnpaidMoney = $project->invoices()->unpaid()->sum('amount');

        $slice = array_slice($timeByMonth, 0, $numMonths);

        $sliceTotal = array_sum($slice);

        $viewVars = [
            'invoices' => $invoices,
            'project' => $project,
            'pageTitle' => $project->name,
            'totalTime' => $totalTime,
            'totalMoney' => $totalMoney,
            'totalUnpaidMoney' => $totalUnpaidMoney,
            'slice' => $slice,
            'sliceTotal' => $sliceTotal,
            'sliceRange' => $numMonths,
        ];

        return view('project.show', $viewVars);
    }

    /**
     * Show the form for editing a project
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
            'pageTitle' => 'Edit project',
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

        return view('project.form', $viewVars);
    }

    /**
     * Update an existing project
     *
     * @param ProjectRequest $request The incoming request
     * @param int            $id      A project primary key
     *
     * @return Response
     */
    public function update(ProjectRequest $request, $id)
    {
        $project = $request->user()->project($id);

        $project->update($request->all());

        MessagingHelper::flashUpdated($project->name);

        return redirect()->route(
            'project.show',
            [$project->id]
        );
    }

    /**
     * Delete a project
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
        $project = $request->user()->project($id);

        $project->delete();

        MessagingHelper::flashDeleted($project->name);

        return redirect()->route('project.index');
    }
}
