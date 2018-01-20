<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeRequest;
use App\Time;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for managing time entries.
 */
class TimeController extends Controller
{
    /**
     * Create a new controller instance
     */
    public function __construct()
    {
        $this->middleware('auth');
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
        $query = $request->get('q');

        $baseQuery = $request->user()->time()->getQuery();

        $time = Time::listing($baseQuery);

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Time::$searchables
            );

            $time = Time::search($time, $searchFields);
        }

        $time = $time->simplepaginate(15);

        $viewvars = [
            'pageTitle' => 'Time',
            'query' => $query,
            'searchFields' => array_keys(Time::$searchables),
            'times' => $time,
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

        $time = new Time();
        $time->project_id = $projectId;

        $previousModel = $request->user()->time();
        if ($projectId !== null) {
            $previousModel->where('project_id', $projectId);
        }

        $previousModel->orderBy('start', 'DESC');
        $previousModel->limit(1);

        $viewVars = [
            'pageTitle' => 'New Time Entry',
            'model' => $time,
            'previousModel' => $previousModel->first(),
            'submission_route' => 'time.store',
            'submission_method' => 'POST',
            'projects' => $projects,
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

        MessagingHelper::flashCreated('time entry');

        return redirect()->route(
            'time.index'
        );
    }

    /**
     * Show the form for editing a time entry.
     *
     * @param Request $request The incoming request
     * @param int $id A primary key
     *
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $time = $request->user()->time()->findOrFail($id);

        $viewVars = [
            'pageTitle' => 'Edit Time Entry',
            'model' => $time,
            'projects' => $request->user()->projectsForMenu(),
            'submission_route' => [
                'time.update',
                $time->id,
            ],
            'submission_method' => 'PUT',
        ];

        return view('time.form', $viewVars);
    }

    /**
     * Update an existing time entry.
     *
     * @param TimeRequest $request The incoming request
     * @param int $id A time entry primary key
     *
     * @return Response
     */
    public function update(TimeRequest $request, $id)
    {
        $time = $request->user()->time()->findOrFail($id);

        $affectedRows = $time->update($request->all());

        MessagingHelper::flashUpdated('time entry');

        return redirect()->route(
            'time.index'
        );
    }

    /**
     * Delete a time entry.
     *
     * Time entries use soft deletion.
     *
     * @param Request $request The incoming request
     * @param int $id A primary key
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->time()->where('id', $id)->delete();

        MessagingHelper::flashDeleted('time entry');

        return redirect()->route('time.index');
    }
}
