<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests;
use App\Http\Requests\ClientRequest;
use App\Http\Controllers\Controller;
use App\Client;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store']]);
        view()->share('app_section', 'client');
    }

    /**
     * Display a list of clients
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $q = null;
        $clients = Client::listing($request->user()->clients());

        if ($request->get('q')) {
            $q = strtolower($request->get('q'));
            $q = filter_var($q, FILTER_SANITIZE_STRING);
            $clients->where('name', 'like', '%' . $q . '%');
        }

        $clients = $clients->simplePaginate(15);

        $viewVars = [
            'page_title' => 'Clients',
            'clients' => $clients,
            'q' => $q,
            'searchRoute' => 'client.index',
            'searchFields' => ['name', 'status', 'locality', 'created', 'active'],
        ];

        return view('clients.list', $viewVars);
    }

    /**
     * Show the form for creating a new client
     *
     * @param Request $request
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
     * Save a new client to the database
     *
     * @param  ClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request)
    {
        $client = new Client;

        $client->active = (int)$request->active;
        $client->name = $request->name;
        $client->contact_name = $request->contact_name;
        $client->contact_email = $request->contact_email;
        $client->address1 = $request->address1;
        $client->address2 = $request->address2;
        $client->city = $request->city;
        $client->locality = $request->locality;
        $client->postal_code = $request->postal_code;
        $client->phone = $request->phone;

        $client->user_id = $request->user()->id;
        $client->save();

        return redirect()->route('client.show', [$client->id])->with('userMessage', 'Success!');
    }

    /**
     * Display a client
     *
     * @param Request $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $client = $request->user()->clients()->findOrFail($id);

        $viewVars = [
            'model' => $client,
            'page_title' => $client->name,
        ];

        return view('clients.show', $viewVars);
    }

    /**
     * Show the form for editing a client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $client = $request->user()->clients()->findOrFail($id);

        $viewVars = [
            'backUrl' => $request->session()->get('returnTo'),
            'model' => $client,
            'page_title' => 'Edit Client',
            'submission_method' => 'PUT',
            'submission_route' => ['client.update', $client->id],
        ];

        return view('clients.form', $viewVars);
    }

    /**
     * Update an existing client
     *
     * @param  ClientRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientRequest $request, $id)
    {
        $client = $request->user()->clients()->findOrFail($id);

        if (empty($request->active)) {
            $client->active = 0;
        }

        $affectedRows = $client->update($request->all());

        $userMessage = $this->userMessageForAffectedRows($affectedRows);

        return redirect()->route('client.show', [$client->id])->with('userMessage', $userMessage);
    }

    /**
     * Delete a client
     *
     * Time entries use soft deletion.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $affectedRows = $request->user()->clients()->where('id', $id)->delete();

        if ($affectedRows == 0) {
            $userMessage = ['warning', 'Nothing deletable was found'];
        } else {
            $userMessage = ['success', 'Deleted successfully'];
        }

        return redirect()->route('client.index')->with('userMessage', $userMessage);
    }
}
