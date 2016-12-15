<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\TimeRequest;
use App\Time;
use Carbon\Carbon;

/**
 * Controller for managing time entries.
 */
class TimeController extends Controller
{
    /**
     * Define middleware and standard date ranges.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('appSection', 'time');
    }

    /**
     * Display a list of time entries.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('q');

        $baseQuery = $request->user()->time()->getQuery();

        $time = Time::listing($baseQuery);

        if ($search !== null) {
            $searchFields = $this->parseSearchQuery(
                $search,
                Time::$searchables
            );

            $time = Time::search($time, $searchFields);
        }

        $time = $time->simplepaginate(15);

        $viewvars = [
            'page_title' => 'Time',
            'search' => $search,
            'times' => $time,
            'searchRoute' => 'time.index',
            'searchFields' => array_keys(Time::$searchables),
        ];

        return view('time.list', $viewvars);
    }

    /**
     * Show the form for creating a new time entry.
     *
     * @param Request $request The incoming request
     *
     * @return response
     */
    public function create(Request $request)
    {
        $projects = $request->user()->projectsForMenu();

        $projectId = $request->input('project', null);
        if (array_key_exists($projectId, $projects) === false) {
            $projectId = null;
        }

        $model = new Time(
            [
                'start' => new Carbon('now'),
                'project_id' => $projectId,
            ]
        );

        $previousModel = $request->user()->time();
        if ($projectId !== null) {
            $previousModel->where('project_id', $projectId);
        }

        $previousModel->orderBy('start', 'DESC');
        $previousModel->limit(1);

        $viewVars = [
            'page_title' => 'Add time',
            'model' => $model,
            'previousModel' => $previousModel->first(),
            'submission_route' => 'time.store',
            'submission_method' => 'POST',
            'projects' => $projects,
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('time.form', $viewVars);
    }

    /**
     * Save a new time entry to the database.
     *
     * @param TimeRequest $request The incoming request
     *
     * @return Response
     */
    public function store(TimeRequest $request)
    {
        $time = new Time();

        $time->project_id        = (int) $request->project_id;
        $time->estimatedDuration = (int) $request->estimatedDuration;
        $time->start             = $request->start;
        $time->minutes           = $request->minutes;
        $time->summary           = $request->summary;

        $time->user()->associate($request->user());
        $time->save();

        $userMessage = $this->successMessage('time entry');

        return redirect()->route(
            'time.index',
            [$time->id]
        )->with('userMessage', $userMessage);
    }

    /**
     * Show the form for editing a time entry.
     *
     * @param Request $request The incoming request
     * @param int     $id      A primary key
     *
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $time = $request->user()->time()->findOrFail($id);

        $viewVars = [
            'page_title' => 'Edit Time',
            'model' => $time,
            'projects' => $request->user()->projectsForMenu(),
            'submission_route' => [
                'time.update',
                $time->id,
            ],
            'submission_method' => 'PUT',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('time.form', $viewVars);
    }

    /**
     * Update an existing time entry.
     *
     * @param TimeRequest $request The incoming request
     * @param int         $id      A time entry primary key
     *
     * @return Response
     */
    public function update(TimeRequest $request, $id)
    {
        $time = $request->user()->time()->findOrFail($id);

        $affectedRows = $time->update($request->all());

        $userMessage = $this->userMessageForAffectedRows($affectedRows);

        return redirect()->route(
            'time.index'
        )->with('userMessage', $userMessage);
    }

    /**
     * Delete a time entry.
     *
     * Time entries use soft deletion.
     *
     * The backto middleware takes care of redirection.
     *
     * @param Request $request The incoming request
     * @param int     $id      A primary key
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->time()->where('id', $id)->delete();

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

        $request->session()->flash('userMessage', $userMessage);
    }
}
