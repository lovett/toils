<?php

namespace App\Http\Controllers;

use App\Client;
use App\Helpers\MessagingHelper;
use Illuminate\Support\Facades\Storage;
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
        view()->share('module', 'invoice');
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
            'collection' => $invoices,
            'emptyMessage' => 'There are no invoices.',
            'pageTitle' => 'Invoices',
            'query' => $query,
            'searchFields' => array_keys(Invoice::$searchables),
        ];

        return view('list', $viewVars);
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
        $clientId = $request->input('client', null);

        if ($clientId) {
            $client = $request->user()->client($clientId)->firstOrFail();
            $projects = $request->user()->projectsByClientForMenu($client->getKey());
        }

        if (is_null($clientId)) {
            $client = null;
            $projects = $request->user()->projectsForMenu();
        }

        $invoice = new Invoice();

        $viewVars = [
            'pageTitle' => 'New Invoice',
            'projects' => $projects,
            'client' => $client,
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

        $projectId = $request->input('project_id', 0);

        $project = $request->user()->projects()->findOrFail($projectId);

        $storedPath = $this->storeReceipt($request);

        if ($storedPath) {
            $invoice->receipt = $storedPath;
        }

        $invoice->fill($request->all());
        $invoice->project()->associate($project);
        $invoice->save();

        MessagingHelper::flashCreated("invoice #{$invoice->number}");

        return redirect()->route('invoice.index', [$invoice->id]);
    }

    /**
     * Render an invoice as a PDF
     *
     * @param Request $request The incoming request
     * @param int $id an invoice primary key
     *
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $invoice = $request->user()->invoice($id);

        $viewVars = [
            'user' => $request->user(),
            'invoice' => $invoice,
            'client' => $invoice->client,
            'pageTitle' => $invoice->name,
        ];

        //return view('invoice.show', $viewVars);

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('invoice.show', $viewVars);

        $filename = sprintf('invoice_%s.pdf', $invoice->number);
        return $pdf->stream($filename);
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
     * @param int $id An invoice primary key
     *
     * @return Response
     */
    public function update(InvoiceRequest $request, $id)
    {
        $invoice = $request->user()->invoice($id);

        $storedPath = $this->storeReceipt($request);

        if ($storedPath) {
            if ($invoice->receipt) {
                $invoice->trashReceipt();
            }
            $invoice->receipt = $storedPath;
        }

        $invoice->update($request->all());

        MessagingHelper::flashUpdated("invoice #{$invoice->number}");

        return redirect()->route('invoice.index', [$invoice->id]);
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
        $invoice = $request->user()->invoice($id);

        $invoice->delete();

        MessagingHelper::flashDeleted("invoice #{$invoice->number}");

        return redirect()->route('invoice.index');
    }

    protected function storeReceipt(InvoiceRequest $request)
    {
        if (!$request->hasFile('receipt')) {
            return null;
        }

        $path = sprintf('receipts/%d', date('Y'));

        return $request->file('receipt')->store($path);
    }

    /**
     * Make a previously-uploaded receipt available for download
     *
     * @param InvoiceRequest $request The incoming request
     * @param int $id An invoice primary key
     *
     * @return Response
     *
     */
    public function receipt(InvoiceRequest $request, $id)
    {

        $invoice = $request->user()->invoice($id);

        abort_unless($invoice->receipt, 404);

        $extension = pathinfo($invoice->receipt, PATHINFO_EXTENSION);
        $name = sprintf('receipt_%s.%s', $invoice->number, $extension);

        return response()->file(Storage::path($invoice->receipt));


    }

}
