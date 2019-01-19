<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeRequest;
use App\Time;
use App\Tag;
use App\Helpers\MessagingHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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
        view()->share('module', 'time');
    }

    /**
     * Display a list of time entries.
     *
     * @param Request $request The incoming request
     *
     * @return View
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
            'collection' => $time,
            'collectionOf' => 'time entries',
            'emptyMessage' => 'There are no time entries.',
            'pageTitle' => 'Time',
            'query' => $query,
            'searchFields' => array_keys(Time::$searchables),
        ];

        return view('list', $viewvars);
    }

    /**
     * Provide autocompletion candidates based on the past invoice for a time entry
     *
     * @param Request $request The incoming request
     * @param int     $id      The id of the project to base the candidates on.
     *
     * @return JsonResponse
     */
    public function suggestByProject(Request $request, int $id)
    {
        $time = $request->user()->timeByProject($id)->firstOrFail();
        return response()->json($time->suggestion);
    }

    /**
     * Show the form for creating a new time entry.
     *
     * @param Request $request The incoming request
     *
     * @return View
     */
    public function create(Request $request)
    {
        $client = null;
        $projects = null;

        $clientId = $request->input('client', null);

        if ($clientId) {
            $client = $request->user()->client($clientId)->firstOrFail();
            $projects = $request->user()->projectsForMenu($client->getKey());
        }

        if ($projects === null) {
            $projects = $request->user()->projectsForMenu();
        }

        $projectId = $request->input('project', null);

        if (array_key_exists($projectId, $projects) === false) {
            $projectId = null;
        }

        $time = new Time();
        $time->project_id = $projectId;

        $viewVars = [
            'pageTitle' => 'New Time Entry',
            'model' => $time,
            'submission_route' => 'time.store',
            'submission_method' => 'POST',
            'projects' => $projects,
            'client' => $client,
        ];

        return view('time.form', $viewVars);
    }

    /**
     * Save a new time entry to the database.
     *
     * @param TimeRequest $request The incoming request
     *
     * @return RedirectResponse
     */
    public function store(TimeRequest $request)
    {
        $time = new Time();

        $time->project_id        = (int) $request->project_id;
        $time->estimatedDuration = (int) $request->estimatedDuration;
        $time->start             = $request->start;
        $time->end               = $request->end;
        $time->summary           = $request->summary;

        $time->user()->associate($request->user());
        $time->save();

        MessagingHelper::flashCreated('time entry');

        return redirect()->route(
            'time.index'
        );
    }

    /**
     * Show the form for editing a time entry.
     *
     * @param Request $request The incoming request
     * @param int     $id      A primary key
     *
     * @return View
     */
    public function edit(Request $request, int $id)
    {
        $time = $request->user()->time()->with('tags')->findOrFail($id);

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
     * @param int         $id      A time entry primary key
     *
     * @return RedirectResponse
     */
    public function update(TimeRequest $request, int $id)
    {
        $time = $request->user()->time()->findOrFail($id);

        $time->update($request->all());

        Tag::syncFromList(
            $time,
            $request->input('tagList')
        );

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
     * @param int     $id      A primary key
     */
    public function destroy(Request $request, int $id)
    {
        $request->user()->time()->where('id', $id)->delete();

        MessagingHelper::flashDeleted('time entry');

        return redirect()->route('time.index');
    }

    /**
     * Close an open time entry.
     *
     * @param Request $request The incoming request
     */
    public function finish(Request $request)
    {
        $id = $request->input('id', null);
        $time = $request->user()->time()->findOrFail($id);

        $time->finish();

        return redirect()->route('time.index');
    }
}
