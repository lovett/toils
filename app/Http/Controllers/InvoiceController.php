<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ClientRequest;
use App\Client;
use App\Invoice;

/**
 * Resource controller for invoices.
 */
class InvoiceController extends Controller
{
    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('appSection', 'invoice');
    }

    /**
     * Display a list of invocies.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('q');

        $baseQuery = $request->user()->invoices();

        $invoices = Invoice::listing($baseQuery);

        if ($search !== null) {
            $searchFields = $this->parseSearchQuery(
                $search,
                Invoice::$searchables
            );

            $invoices = Invoice::search($invoices, $searchFields);
        }

        $invoices = $invoices->simplePaginate(15);

        $viewVars = [
            'page_title' => 'Invoices',
            'invoices' => $invoices,
            'search' => $search,
            'searchFields' => array_keys(Invoice::$searchables),
        ];

        return view('invoices.list', $viewVars);
    }

    /**
     * Show the form for creating a new invoice.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $invoice = new Invoice();

        $viewVars = [
            'page_title' => 'Add an invoice',
            'projects' => $request->user()->projectsForMenu(),
            'model' => $invoice,
            'submission_route' => 'invoice.store',
            'submission_method' => 'POST',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('invoices.form', $viewVars);
    }

    /**
     * Save a new invoice to the database.
     *
     * @param InvoiceRequest $request The incoming request
     *
     * @return Response
     */
    public function store(InvoiceRequest $request)
    {
        $invoice = new Invoice();

        // $invoice->active       = (int) $request->active;
        // $invoice->name         = $request->name;
        // $invoice->contactName  = $request->contactName;
        // $invoice->contactEmail = $request->contactEmail;
        // $invoice->address1     = $request->address1;
        // $invoice->address2     = $request->address2;
        // $invoice->city         = $request->city;
        // $invoice->locality     = $request->locality;
        // $invoice->postalCode   = $request->postalCode;
        // $invoice->phone        = $request->phone;

        $invoice->user()->associate($request->user());
        $invoice->save();

        $userMessage = $this->successMessage('invoice');

        return redirect()->route(
            'invoice.show',
            [$invoice->id]
        )->with('userMessage', $userMessage);
    }

    /**
     * Display an invoice.
     *
     * @param Request $request The incoming request
     * @param int     $id      an invoice primary key
     *
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $invoice = $request->user()->invoices()->findOrFail($id);

        $viewVars = [
            'model' => $invoice,
            'page_title' => $invoice->name,
        ];

        return view('invoices.show', $viewVars);
    }

    /**
     * Show the form for editing an invoice.
     *
     * @param Request $request The incoming request
     * @param int     $id      an invoice primary key
     *
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $invoice = $request->user()->invoices()->findOrFail($id);

        $viewVars = [
            'backUrl' => $request->session()->get('returnTo'),
            'projects' => $request->user()->projectsForMenu(),
            'model' => $invoice,
            'page_title' => 'Edit Invoice',
            'submission_method' => 'PUT',
            'submission_route' => [
                'invoice.update',
                $invoice->id,
            ],
        ];

        return view('invoices.form', $viewVars);
    }

    /**
     * Update an existing invoice.
     *
     * @param InvoiceRequest $request The incoming request
     * @param int           $id      an invoice primary key
     *
     * @return Response
     */
    public function update(InvoiceRequest $request, $id)
    {
        $invoice = Invoice::find($id);

        $affectedRows = $invoice->update($request->all());

        if ($invoice->active === 0) {
            // An inactive invoice should not have active projects.
            $projects = $invoice->projects();
            $projects->update(['active' => false]);
        }

        $userMessage = $this->userMessageForAffectedRows($affectedRows);

        return redirect()->route(
            'invoice.show',
            [$invoice->id]
        )->with('userMessage', $userMessage);
    }

    /**
     * Delete an invoice.
     *
     * Invoices use soft deletion.
     *
     * @param Request $request The incoming request
     * @param int     $id      An invoice primary key
     *
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->invoices()->where('id', $id)->delete();

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

        return redirect()->route('invoice.index')->with(
            'userMessage',
            $userMessage
        );
    }
}
