<?php

namespace App\Http\Controllers;

use App\Estimate;
use App\Helpers\MessagingHelper;
use App\Http\Requests\EstimateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Resource controller for estimates
 */
class EstimateController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('module', 'estimate');
    }

    /**
     * Display a list of estimates.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $baseQuery = $request->user()->estimates()->getQuery();
        $estimates = Estimate::listing($baseQuery);

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Estimate::$searchables
            );

            $estimates = Estimate::search($estimates, $searchFields);
        }

        $estimates = $estimates->simplePaginate(15);

        $viewVars = [
            'collection' => $estimates,
            'emptyMessage' => 'There are no estimates.',
            'pageTitle' => 'Estimate List',
            'query' => $query,
            'searchFields' => array_keys(Estimate::$searchables),
        ];

        return view('list', $viewVars);
    }

    /**
     * Show the form for creating a new estimate
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $estimate = new Estimate();

        $estimate->active = true;

        $clients = $request->user()->clientsForMenu();

        $viewVars = [
            'clients' => $clients,
            'model' => $estimate,
            'pageTitle' => 'New Estimate',
            'statuses' => $estimate->statuses,
            'submission_method' => 'POST',
            'submission_route' => 'estimate.store',
        ];

        return view('estimate.form', $viewVars);
    }

    /**
     * Save a new estimate to the database.
     *
     * @param EstimateRequest $request The incoming request
     *
     * @return Response
     */
    public function store(EstimateRequest $request)
    {
        $estimate = new Estimate();

        $estimate->fill($request->all());
        $estimate->save();

        $estimate->users()->attach($request->user());

        MessagingHelper::flashCreated($estimate->name);

        return redirect()->route('estimate.index', [$estimate->id]);
    }

    /**
     * Display an estimate
     *
     * @param Request $request The incoming request
     * @param int $id An Estimate primary key
     *
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $estimate = $request->user()->estimates()->findOrFail($id);

        $viewVars = [
            'model' => $estimate,
            'pageTitle' => $estimate->name,
        ];

        return view('estimate.show', $viewVars);
    }

    /**
     * Show the form for editing an estimate
     *
     * @param Request $request The incoming request
     * @param int $id An Estimate primary key
     *
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $estimate = $request->user()->estimate($id);

        $clients = $request->user()->clientsForMenu();

        $viewVars = [
            'model' => $estimate,
            'pageTitle' => "Edit {$estimate->name}",
            'statuses' => $estimate->statuses,
            'clients' => $clients,
            'submission_method' => 'PUT',
            'submission_route' => [
                'estimate.update',
                $estimate->id,
            ],
        ];

        return view('estimate.form', $viewVars);
    }

    /**
     * Update an existing estimate
     *
     * @param EstimateRequest $request The incoming request
     * @param int $id An estimate primary key
     *
     * @return Response
     */
    public function update(EstimateRequest $request, $id)
    {
        $estimate = $request->user()->estimate($id);

        $estimate->update($request->all());

        MessagingHelper::flashUpdated($estimate->name);

        return redirect()->route('estimate.index');
    }

    /**
     * Delete an estimate
     *
     * @param Request $request The incoming request
     * @param int $id An Estimate primary key
     *
     * @return Response
     */
    public function destroy(Request $request, $id)
    {

        $estimate = $request->user()->estimate($id);

        $estimate->delete();

        MessagingHelper::flashDeleted($estimate->name);

        return redirect()->route('estimate.index');
    }
}
