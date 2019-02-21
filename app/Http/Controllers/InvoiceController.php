<?php

namespace App\Http\Controllers;

use App\Client;
use App\Helpers\MessagingHelper;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\InvoiceRequest;
use App\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controller for managing invoices.
 */
class InvoiceController extends Controller
{


    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('module', 'invoice');
        view()->share('maxFileSize', ini_get('upload_max_filesize'));
    }

    /**
     * Display a list of invoices.
     *
     * @param InvoiceRequest $request The incoming request
     *
     * @return View
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $baseQuery = $request->user()->invoices();

        $invoices = Invoice::listing($baseQuery);

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
            'collectionOf' => 'invoices',
            'pageTitle' => 'Invoices',
            'query' => $query,
            'searchFields' => array_keys(Invoice::$searchables),
        ];

        return view('list', $viewVars);
    }

    /**
     * Provide autocompletion candidates based on the past invoice for a project.
     *
     * @param InvoiceRequest $request The incoming request
     * @param int            $id      The id of the project to base the candidates on.
     *
     * @return JsonResponse
     */
    public function suggestByProject(Request $request, int $id = 0)
    {
        $project = $request->user()->project($id)->firstOrFail();

        $invoice = $project->invoices()->newest(1)->firstOrFail();

        return response()->json($invoice->suggestion);
    }

    /**
     * Show the form for creating a new invoice.
     *
     * @param InvoiceRequest $request The incoming request
     *
     * @return View
     */
    public function create(Request $request)
    {
        $clientId = $request->input('client', null);
        $projectId = $request->input('project', null);

        $project = null;
        $client = null;
        $projects = $request->user()->projectsForMenu();

        if ($projectId) {
            $project = $request->user()->project($projectId)->with('client')->firstOrFail();
            $client = $project->client;
            $projects = $request->user()->projectsForMenu($client->getKey());
        }

        if ($clientId) {
            $project = null;
            $client = $request->user()->client($clientId)->firstOrFail();
            $projects = $request->user()->projectsForMenu($client->getKey());
        }

        $invoice = new Invoice();
        $invoice->project_id = $projectId;

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
     * @return RedirectResponse
     */
    public function store(InvoiceRequest $request)
    {
        $invoice = new Invoice();

        $projectId = $request->input('project_id');

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
     * @param InvoiceRequest $request The incoming request
     * @param int            $id      An invoice primary key
     *
     * @return Response
     */
    public function show(Request $request, int $id)
    {
        $invoice = $request->user()->invoice($id);

        $viewVars = [
            'user' => $request->user(),
            'invoice' => $invoice,
            'client' => $invoice->client,
            'pageTitle' => $invoice->name,
        ];

        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadView('invoice.show', $viewVars);

        $filename = sprintf('invoice_%s.pdf', $invoice->number);

        return $pdf->stream($filename);
    }

    /**
     * Show the form for editing an invoice.
     *
     * @param InvoiceRequest $request The incoming request
     * @param int            $id      An invoice primary key
     *
     * @return View
     */
    public function edit(Request $request, int $id)
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
     * @param int            $id      An invoice primary key
     *
     * @return RedirectResponse
     */
    public function update(InvoiceRequest $request, int $id)
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
     * @param InvoiceRequest $request The incoming request
     * @param int            $id      An invoice primary key
     *
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $invoice = $request->user()->invoice($id);

        $invoice->delete();

        MessagingHelper::flashDeleted("invoice #{$invoice->number}");

        return redirect()->route('invoice.index');
    }

    /**
     * Capture the receipt for a paid invoice into storage.
     *
     * @param InvoiceRequest $request The incoming request
     */
    protected function storeReceipt(InvoiceRequest $request)
    {
        if ($request->hasFile('receipt') === false) {
            return null;
        }

        $path = sprintf('receipts/%d', date('Y'));

        return $request->file('receipt')->store($path);
    }

    /**
     * Make a previously-uploaded receipt available for download
     *
     * @param InvoiceRequest $request The incoming request
     * @param int            $id      An invoice primary key
     *
     * @return BinaryFileResponse
     */
    public function receipt(InvoiceRequest $request, int $id)
    {
        $invoice = $request->user()->invoice($id);

        abort_unless($invoice->receipt, 404);

        $extension = pathinfo($invoice->receipt, PATHINFO_EXTENSION);

        $name = sprintf('receipt_%s.%s', $invoice->number, $extension);

        return response()->download(
            Storage::path($invoice->receipt),
            $name
        );
    }
}
