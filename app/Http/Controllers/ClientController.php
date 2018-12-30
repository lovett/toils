<?php

namespace App\Http\Controllers;

use App\Client;
use App\Invoice;
use App\Time;
use App\Estimate;
use App\Helpers\MessagingHelper;
use App\Http\Requests\ClientRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

/**
 * Resource controller for clients
 */
class ClientController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        view()->share('module', 'client');
    }

    /**
     * Display a list of clients.
     *
     * @param ClientRequest $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $baseQuery = $request->user()->clients()->getQuery();

        $clients = Client::listing($baseQuery);

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Client::$searchables
            );

            $clients = Client::search($clients, $searchFields);
        }

        $clients = $clients->simplePaginate(15);

        $viewVars = [
            'collection' => $clients,
            'emptyMessage' => 'There are no clients.',
            'pageTitle' => 'Client List',
            'query' => $query,
            'searchFields' => array_keys(Client::$searchables),
        ];

        return view('list', $viewVars);
    }

    /**
     * Show the form for creating a new client.
     *
     * @return Response
     */
    public function create()
    {
        $client = new Client();

        $client->active = true;

        $viewVars = [
            'pageTitle' => 'New Client',
            'model' => $client,
            'submission_route' => 'client.store',
            'submission_method' => 'POST',
        ];

        return view('client.form', $viewVars);
    }

    /**
     * Save a new client to the database.
     *
     * @param ClientRequest $request The incoming request
     *
     * @return Response
     */
    public function store(ClientRequest $request)
    {
        $client = new Client();

        $client->active       = (int) $request->active;
        $client->name         = $request->name;
        $client->contactName  = $request->contactName;
        $client->contactEmail = $request->contactEmail;
        $client->address1     = $request->address1;
        $client->address2     = $request->address2;
        $client->city         = $request->city;
        $client->locality     = $request->locality;
        $client->postalCode   = $request->postalCode;
        $client->phone        = $request->phone;

        $client->save();
        $client->users()->attach($request->user());

        MessagingHelper::flashCreated($client->name);

        return redirect()->route(
            'client.show',
            [$client->id]
        );
    }

    /**
     * Display a client
     *
     * A homepage for knowing everything about a client: its projects,
     * invoices, time entries, contact information.
     *
     * @param Request $request The incoming request
     * @param int     $id      A client primary key
     *
     * @return Response
     */
    public function show(Request $request, int $id)
    {
        $fetchLimit = 5;

        $client = $request->user()->clients()->with('projects')->findOrFail($id);

        $estimateBaseQuery = $client->estimates()->newest($fetchLimit)->getQuery();
        $estimates = Estimate::listing($estimateBaseQuery)->get();

        $invoiceBaseQuery = $client->invoices()->newest($fetchLimit)->getQuery();
        $invoices = Invoice::listing($invoiceBaseQuery)->get();

        $timeBaseQuery = $client->time()->newest($fetchLimit)->getQuery();
        $time = Time::listing($timeBaseQuery)->get();

        $viewVars = [
            'model' => $client,
            'pageTitle' => $client->name,
            'invoices' => $invoices,
            'estimates' => $estimates,
            'time' => $time,
            'stats' => $client->stats(),
        ];

        return view('client.show', $viewVars);
    }

    /**
     * Show the form for editing a client
     *
     * @param ClientRequest $request The incoming request
     * @param int           $id      A client primary key
     *
     * @return Response
     */
    public function edit(ClientRequest $request, int $id)
    {
        $client = $request->user()->clients()->findOrFail($id);

        $viewVars = [
            'backUrl' => $request->session()->get('returnTo'),
            'model' => $client,
            'pageTitle' => "Edit {$client->name}",
            'submission_method' => 'PUT',
            'submission_route' => [
                'client.update',
                $client->id,
            ],
        ];

        return view('client.form', $viewVars);
    }

    /**
     * Update an existing client
     *
     * @param ClientRequest $request The incoming request
     * @param int           $id      A client primary key
     *
     * @return Response
     */
    public function update(ClientRequest $request, int $id)
    {
        $client = $request->user()->client($id);

        $client->update($request->all());

        MessagingHelper::flashUpdated($client->name);

        return redirect()->route(
            'client.show',
            [$client->id]
        );
    }

    /**
     * Delete a client
     *
     * @param ClientRequest $request The incoming request
     * @param int           $id      A client primary key
     *
     * @return Response
     */
    public function destroy(ClientRequest $request, int $id)
    {
        $client = $request->user()->client($id);

        $client->delete();

        MessagingHelper::flashDeleted($client->name);

        return redirect()->route('client.index');
    }
}
