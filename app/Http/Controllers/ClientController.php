<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ClientRequest;
use App\Client;
use Illuminate\Support\Collection;

/**
 * Resource controller for clients.
 */
class ClientController extends Controller
{
    /**
     * Set middleware and shared view values.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('appSection', 'clients');
    }

    /**
     * Display a list of clients.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->get('q');

        $baseQuery = $request->user()->clients()->getQuery();

        $clients = Client::listing($baseQuery);

        if ($search !== null) {
            $searchFields = $this->parseSearchQuery(
                $search,
                Client::$searchables
            );

            $clients = Client::search($clients, $searchFields);
        }

        $clients = $clients->simplePaginate(15);

        $viewVars = [
            'page_title' => 'Clients',
            'clients' => $clients,
            'search' => $search,
            'searchFields' => array_keys(Client::$searchables),
        ];

        return view('clients.list', $viewVars);
    }

    /**
     * Show the form for creating a new client.
     *
     * @param Request $request The incoming request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $client = new Client();

        $client->active = true;

        $viewVars = [
            'page_title' => 'Add a client',
            'model' => $client,
            'submission_route' => 'client.store',
            'submission_method' => 'POST',
            'backUrl' => $request->session()->get('returnTo'),
        ];

        return view('clients.form', $viewVars);
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

        $client->user()->associate($request->user());
        $client->save();

        $userMessage = $this->successMessage('client');

        return redirect()->route(
            'client.show',
            [$client->id]
        )->with('userMessage', $userMessage);
    }

    /**
     * Display a client.
     *
     * @param Request $request The incoming request
     * @param int     $id      A client primary key
     *
     * @return Response
     */
    public function show(Request $request, $id)
    {
        $client = $request->user()->clients()->with('projects')->findOrFail($id);

        $invoices = $client->invoices()->orderBy('sent')->take(10)->get();

        $viewVars = [
            'model' => $client,
            'page_title' => $client->name,
            'invoices' => $invoices,
        ];

        return view('clients.show', $viewVars);
    }

    /**
     * Show the form for editing a client.
     *
     * @param Request $request The incoming request
     * @param int     $id      A client primary key
     *
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $client = $request->user()->clients()->findOrFail($id);

        $viewVars = [
            'backUrl' => $request->session()->get('returnTo'),
            'model' => $client,
            'page_title' => 'Edit Client',
            'submission_method' => 'PUT',
            'submission_route' => [
                'client.update',
                $client->id,
            ],
        ];

        return view('clients.form', $viewVars);
    }

    /**
     * Update an existing client.
     *
     * @param ClientRequest $request The incoming request
     * @param int           $id      A client primary key
     *
     * @return Response
     */
    public function update(ClientRequest $request, $id)
    {
        $client = Client::find($id);

        $affectedRows = $client->update($request->all());

        if ($client->active === 0) {
            // An inactive client should not have active projects.
            $projects = $client->projects();
            $projects->update(['active' => false]);
        }

        $userMessage = $this->userMessageForAffectedRows($affectedRows);

        return redirect()->route(
            'client.show',
            [$client->id]
        )->with('userMessage', $userMessage);
    }

    /**
     * Delete a client.
     *
     * Time entries use soft deletion.
     *
     * @param Request $request The incoming request
     * @param int     $id      A client primary key
     *
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->clients()->where('id', $id)->delete();

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

        return redirect()->route('client.index')->with(
            'userMessage',
            $userMessage
        );
    }
}
