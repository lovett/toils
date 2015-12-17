<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Client;

class ClientController extends Controller
{
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
            'clients' => $clients,
            'next_action' => [
                'label' => 'Add a client',
                'link' => route('client.create')
            ]
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
            'client' => new Client(),
            'next_action' => [
                'label' => 'Cancel',
                'link' => route('client.index')
            ]

        ];

        return view('clients.form', $viewVars);
    }

    /**
     * Save a new client to the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $client = new Client;

        $client->active = $request->active;
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
    public function edit($id)
    {
    }

    /**
     * Update an existing client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Delete a client
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
