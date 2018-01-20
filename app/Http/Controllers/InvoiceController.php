<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Requests\InvoiceRequest;
use App\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a list of invoices.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {

        $query = $request->get('q');

        $invoices = $request->user()->invoices();

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Invoice::$searchables
            );

            $invoices = Invoice::search($invoices, $searchFields);
        }

        $invoices = $invoices->simplePaginate(15);

        $viewVars = [
            'pageTitle' => 'Invoices',
            'invoices' => $invoices,
            'query' => $query,
            'searchFields' => array_keys(Invoice::$searchables),
        ];

        return view('invoice.list', $viewVars);
    }

    /**
     * Provide autocompletion candidates based on the past invoice for a project.
     *
     * @param Request $request  The incoming request
     * @param int     $id       The id of the project to base the candidates on.
     *
     * @return Response A json response.
     */
    public function suggestByProject(Request $request, $id = 0)
    {
        $id = (int)$id;
        $invoice = $request->user()->project($id)->invoices()->recent(1)->firstOrFail();
        return response()->json($invoice->suggestion);
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
            'pageTitle' => 'New Invoice',
            'projects' => $request->user()->projectsForMenu(),
            'model' => $invoice,
            'submission_route' => 'invoice.store',
            'submission_method' => 'POST',
        ];

        return view('invoice.form', $viewVars);
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

        $invoice->save();
        $invoice->user()->associate($request->user());

        MessagingHelper::flashCreated($invoice->name);

        return redirect()->route(
            'invoice.show',
            [$invoice->id]
        );
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
            'pageTitle' => $invoice->name,
        ];

        return view('invoice.show', $viewVars);
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
        $invoice = $request->user()->invoice($id);

        $viewVars = [
            'projects' => $request->user()->projectsForMenu(),
            'model' => $invoice,
            'pageTitle' => "Edit Invoice {$invoice->number}",
            'submission_method' => 'PUT',
            'submission_route' => [
                'invoice.update',
                $invoice->id,
            ],
        ];

        return view('invoice.form', $viewVars);
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
