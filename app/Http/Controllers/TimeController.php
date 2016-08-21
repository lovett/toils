<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Requests\TimeRequest;
use App\Http\Controllers\Controller;
use App\Time;
use DatePeriod;
use DateInterval;
use DateTime;
use Carbon\Carbon;
use Illuminate\Database\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Controller for managing time entries
 */
class TimeController extends Controller
{


    /**
     * Define middleware and standard date ranges
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('appSection', 'time');

        view()->share('dateFormat', 'Y-m-d');

        view()->share(
            'ranges',
            [
                'today' => new DateTime(),

                'yesterday' => new DateTime('-1 day'),

                '2-days-ago' => new DateTime('-2 day'),

                '3-days-ago' => new DateTime('-3 day'),

                'month' => new DatePeriod(
                    new DateTime('Jan 1'),
                    new DateInterval('P1M'),
                    new DateTime('Dec 31')
                ),

                'day' => new DatePeriod(
                    new DateTime('Jan 1'),
                    new DateInterval('P1D'),
                    new DateTime('Feb 1')
                ),

                'year' => new DatePeriod(
                    new DateTime('-5 years'),
                    new DateInterval('P1Y'),
                    new DateTime('first day of next year')
                ),

                'hour' => new DatePeriod(
                    new DateTime('Jan 1 1:00'),
                    new DateInterval('PT1H'),
                    new DateTime('Jan 1 13:00')
                ),

                'minute' => new DatePeriod(
                    new DateTime('Jan 1 00:00'),
                    new DateInterval('PT5M'),
                    new DateTime('Jan 1 01:00')
                ),
            ]
        );
    }

    /**
     * Display a list of time entries
     *
     * @param Request $request The incoming request.
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
     * @param Request $request The incoming request.
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
                'project_id' => $projectId
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
     * Save a new time entry to the database
     *
     * @param Request $request The incoming request.
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
     * Show the form for editing a time entry
     *
     * @param Request $request The incoming request.
     * @param integer $id      A primary key.
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
     * Update an existing time entry
     *
     * @param TimeRequest $request The incoming request.
     * @param integer     $id      A time entry primary key.
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
     * Delete a time entry
     *
     * Time entries use soft deletion.
     *
     * The backto middleware takes care of redirection.
     *
     * @param Request $request The incoming request.
     * @param integer $id      A primary key.
     *
     * @return void
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
