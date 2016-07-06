<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
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
        view()->share('app_section', 'time');

        view()->share(
            'ranges',
            [
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
                    new DateTime('-366 day'),
                    new DateInterval('P1Y'),
                    new DateTime('+366 day')
                ),

                'hour' => range(1, 12),
                'minute' => range(0, 59, 5),
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
        $search = null;

        $time = Time::listing($request->user()->time());

        if ($request->get('q')) {
            $search = strtolower($request->get('q'));
            $search = filter_var($search, FILTER_SANITIZE_STRING);
            $time->where('summary', 'like', '%' . $search . '%');
        }

        $time = $time->simplepaginate(15);

        $viewvars = [
            'page_title' => 'Time',
            'search' => $search,
            'times' => $time,
            'searchRoute' => 'time.index',
            'searchfields' => [
                'summary',
                'start',
                'minutes',
                'end',
            ],
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
        $record = new Time();

        $record->start = new Carbon('now');

        $viewVars = [
            'page_title' => 'Add time',
            'record' => new Time(),
            'submission_route' => 'time.store',
            'submission_method' => 'POST',
            'projects' => $request->user()->projectsForMenu(),
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('time.form', $viewVars);
    }

    /**
     * Save a new time entry to the database
     *
     * @param Request $request The incoming request.
     *
     * @return void
     */
    public function store(Request $request)
    {
    }

    /**
     * Time entries do not have a show view
     *
     * @param Request $request The incoming request.
     * @param integer $id      A primary key.
     *
     * @return void
     */
    public function show(Request $request, $id)
    {
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
     * @param Request $request The incoming request.
     * @param integer $id      A primary key.
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
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
