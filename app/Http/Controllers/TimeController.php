<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Time;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use DatePeriod;
use DateInterval;
use DateTime;
use Carbon\Carbon;
use Illuminate\Database\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TimeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store', 'update', 'destroy']]);
        view()->share('app_section', 'time');

        view()->share('ranges', [
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
        ]);
    }

    /**
     * Display a list of time entries
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $records = $request->user()->time()->with('project')->orderBy('start', 'desc')->simplePaginate(15);

        $q = $request->get('q');

        $viewVars = [
            'page_title' => 'Time',
            'q' => $q,
            'records' => $records,
            'search_route' => 'time.index'
        ];

        return view('time.list', $viewVars);
    }

    /**
     * Show the form for creating a new time entry.
     *
     * @param Request $request
     * @return Response
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
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Time entries do not have a show view
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing a time entry
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $record = $request->user()->time()->findOrFail($id);

        $viewVars = [
            'page_title' => 'Edit Time',
            'record' => $record,
            'projects' => $request->user()->projectsForMenu(),
            'submission_route' => ['time.update', $record->id],
            'submission_method' => 'PUT',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('time.form', $viewVars);
    }

    /**
     * Update an existing time entry
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
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
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->time()->where('id', $id)->delete();

        if ($affectedRows == 0) {
            $userMessage = ['warning', 'Nothing deletable was found'];
        } else {
            $userMessage = ['success', 'Deleted successfully'];
        }

        $request->session()->flash('userMessage', $userMessage);
    }
}
