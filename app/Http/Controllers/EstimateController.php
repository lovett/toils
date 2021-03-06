<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Helpers\MessagingHelper;
use App\Http\Requests\EstimateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Parsedown;

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
     * @return View
     */
    public function index(Request $request): View
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
            'collectionOf' => 'estimates',
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
     * @return View
     */
    public function create(Request $request): View
    {
        $estimate = new Estimate();

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
     * @return RedirectResponse
     */
    public function store(EstimateRequest $request): RedirectResponse
    {
        $estimate = new Estimate();

        $estimate->fill($request->all());
        $estimate->save();

        $estimate->users()->attach($request->user());

        MessagingHelper::flashCreated($estimate->name);

        return redirect()->route('estimate.index', [$estimate->id]);
    }

    /**
     * Render an estimate as a PDF
     *
     * @param EstimateRequest $request The incoming request
     * @param int             $id      An estimate primary key
     *
     * @return Response
     */
    public function show(Request $request, int $id): Response
    {
        $estimate = $request->user()->estimate($id);

        $parser = new Parsedown();

        $statementOfWork = $parser->text($estimate->statement_of_work);

        $viewVars = [
            'user' => $request->user(),
            'name' => $estimate->name,
            'date' => $estimate->submitted,
            'statementOfWork' => $statementOfWork,
        ];

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('estimate.show', $viewVars);

        $filename = sprintf(
            'estimate_%s.pdf',
            Str::slug($estimate->name)
        );

        return $pdf->stream($filename);
    }

    /**
     * Show the form for editing an estimate
     *
     * @param Request $request The incoming request
     * @param int     $id      An Estimate primary key
     *
     * @return View
     */
    public function edit(Request $request, int $id): View
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
     * Save a modified estimate to the database.
     *
     * @param EstimateRequest $request The incoming request
     * @param int             $id      An estimate primary key
     *
     * @return RedirectResponse
     */
    public function update(EstimateRequest $request, int $id): RedirectResponse
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
     * @param int     $id      An Estimate primary key
     *
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $estimate = $request->user()->estimate($id)->firstOrFail();

        $estimate->delete();

        MessagingHelper::flashDeleted($estimate->name);

        return redirect()->route('estimate.index');
    }
}
