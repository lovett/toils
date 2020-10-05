<?php

namespace App\Http\Controllers;

use App\Helpers\MessagingHelper;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Time;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
     * @return View
     */
    public function index(Request $request)
    {
        $query = $request->get('q');

        $baseQuery = $request->user()->clients()->getQuery();

        $clients = Client::listing($baseQuery);

        if ($query !== null) {
            $searchFields = $this->parseSearchQuery(
                $query,
                Client::$searchables,
                Client::$searchAliases
            );

            $clients = Client::search($clients, $searchFields);
        }

        $clients = $clients->simplePaginate(15);

        $viewVars = [
            'collection' => $clients,
            'collectionOf' => 'clients',
            'pageTitle' => 'Client List',
            'query' => $query,
            'searchFields' => array_keys(Client::$searchables),
        ];

        return view('list', $viewVars);
    }

    /**
     * Show the form for creating a new client.
     *
     * @return View
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
     * @return RedirectResponse
     */
    public function store(ClientRequest $request)
    {
        $client = new Client();

        $client->active       = (bool) $request->active;
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
     * @return View
     */
    public function show(Request $request, int $id)
    {
        $fetchLimit = 5;

        $client = $request->user()->clients()->with('projects')->findOrFail($id);

        $numMonths = 6;

        $timeByMonth = Time::byInterval(
            $client,
            $request->user(),
            'month',
            $numMonths
        );

        $numWeeks = 6;
        $timeByWeek = Time::byInterval(
            $client,
            $request->user(),
            'week',
            $numWeeks
        );

        $estimateBaseQuery = $client->estimates()->newest($fetchLimit)->getQuery();
        $estimates = Estimate::listing($estimateBaseQuery)->get();

        $invoiceBaseQuery = $client->invoices()->newest($fetchLimit)->getQuery();
        $invoices = Invoice::listing($invoiceBaseQuery)->get();

        $timeBaseQuery = $client->time()->newest($fetchLimit)->getQuery();
        $time = Time::listing($timeBaseQuery)->get();

        $monthSlice = array_slice($timeByMonth, 0, $numMonths);
        $weekSlice = array_slice($timeByWeek, 0, $numWeeks);

        $monthSliceTotal = array_sum($monthSlice);
        $weekSliceTotal = array_sum($weekSlice);

        $viewVars = [
            'model' => $client,
            'pageTitle' => $client->name,
            'invoices' => $invoices,
            'estimates' => $estimates,
            'monthSlice' => $monthSlice,
            'monthSliceTotal' => $monthSliceTotal,
            'weekSlice' => $weekSlice,
            'weekSliceTotal' => $weekSliceTotal,
            'monthSliceRange' => $numMonths,
            'weekSliceRange' => $numWeeks,
            'sliceRange' => $numMonths,
            'time' => $time,
            'stats' => $client->stats(),
        ];

        return view('client.show', $viewVars);
    }

    /**
     * Show the form for editing a client
     *
     * @param Request $request The incoming request
     * @param int     $id      A client primary key
     *
     * @return View
     */
    public function edit(Request $request, int $id)
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
     * Save a modified client to the database.
     *
     * @param ClientRequest $request The incoming request
     * @param int           $id      A client primary key
     *
     * @return RedirectResponse
     */
    public function update(ClientRequest $request, int $id)
    {
        $client = $request->user()->client($id)->firstOrFail();

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
     * @return RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $client = $request->user()->client($id)->firstOrFail();

        $client->delete();

        MessagingHelper::flashDeleted($client->name);

        return redirect()->route('client.index');
    }
}
