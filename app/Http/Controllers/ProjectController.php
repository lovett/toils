<?php

namespace App\Http\Controllers;

use App\Client;
use App\Invoice;
use App\Helpers\MessagingHelper;
use App\Http\Requests\ProjectRequest;
use App\Project;
use App\Time;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

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
     * @return View
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $baseQuery = $request->user()->projects();

        $projects = Project::listing($baseQuery);

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Project::$searchables,
                Project::$searchAliases
            );

            $projects = Project::search($projects, $searchFields);
        }

        $projects = $projects->simplePaginate(15);

        $viewVars = [
            'collection' => $projects,
            'collectionOf' => 'projects',
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
     * @return View
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

        $clientId = $request->input('client_id');

        $client = $request->user()->clients()->findOrFail($clientId);

        $project->name = $request->input('name');
        $project->active = $request->input('active');
        $project->billable = $request->input('billable');
        $project->taxDeducted = $request->input('taxDeducted');

        $project->client()->associate($client);
        $project->save();

        MessagingHelper::flashCreated($project->name);

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
     * @return View
     */
    public function show(Request $request, int $id)
    {
        $fetchLimit = 5;

        $project = $request->user()->projects()
                 ->with('client')
                 ->findOrFail($id);

        $numMonths = 6;

        $timeByMonth = Time::byInterval(
            $project,
            $request->user(),
            'month',
            $numMonths
        );

        $numWeeks = 6;
        $timeByWeek = Time::byInterval(
            $project,
            $request->user(),
            'week',
            $numWeeks
        );

        $baseQuery = $request->user()->time()->getQuery();

        $time = Time::listing($baseQuery)
              ->where('times.project_id', $project->getKey())
              ->newest($fetchLimit)->get();

        $invoiceBaseQuery = $project->invoices()->newest($fetchLimit)->getQuery();
        $invoices = Invoice::listing($invoiceBaseQuery)->get();

        $monthSlice = array_slice($timeByMonth, 0, $numMonths);
        $weekSlice = array_slice($timeByWeek, 0, $numWeeks);

        $monthSliceTotal = array_sum($monthSlice);
        $weekSliceTotal = array_sum($weekSlice);

        $viewVars = [
            'invoices' => $invoices,
            'project' => $project,
            'pageTitle' => $project->name,
            'monthSlice' => $monthSlice,
            'monthSliceTotal' => $monthSliceTotal,
            'weekSlice' => $weekSlice,
            'weekSliceTotal' => $weekSliceTotal,
            'monthSliceRange' => $numMonths,
            'weekSliceRange' => $numWeeks,
            'stats' => $project->stats(),
            'time' => $time,
            'totalTimeRemaining' => $project->totalTimeRemaining,
            'weeklyTimeRemaining' => $project->weeklyTimeRemaining,
        ];

        return view('project.show', $viewVars);
    }

    /**
     * Show the form for editing a project
     *
     * @param Request $request The incoming request
     * @param int     $id      A project primary key
     *
     * @return View
     */
    public function edit(Request $request, int $id)
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
     * @return RedirectResponse
     */
    public function update(ProjectRequest $request, int $id)
    {
        $project = $request->user()->project($id)->firstOrFail();

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
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $project = $request->user()->project($id)->firstOrFail();

        $project->delete();

        MessagingHelper::flashDeleted($project->name);

        return redirect()->route('project.index');
    }
}
