<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ClientRequest;
use App\Http\Controllers\Controller;
use App\Client;

class ClientController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a list of clients
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $clients = Client::all();

        $viewVars = [
            'page_title' => 'Clients',
            'models' => $clients,
        ];

        return view('clients.list', $viewVars);
    }

    /**
     * Show the form for creating a new client
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $viewVars = [
            'page_title' => 'Add a client',
            'model' => new Client(),
            'submission_route' => 'client.store',
            'submission_method' => 'POST',
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

        return redirect()->route('clients');
    }

    /**
     * Display a client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing a client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $client = Client::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

        $viewVars = [
            'page_title' => 'Edit Client',
            'model' => $client,
            'submission_route' => ['client.update', $client->id],
            'submission_method' => 'PUT'
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
        $client = Client::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();


        if (empty($request->active)) {
            $client->active = 0;
        }

        $client->update($request->all());

        return redirect()->route('clients');
    }

    /**
     * Delete a client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $client = Client::where('id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

        $client->delete();

        return redirect()->route('clients');
    }
}
