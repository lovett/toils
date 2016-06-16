<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class TimeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('returnable', ['only' => ['index', 'show']]);
        $this->middleware('backto', ['only' => ['store', 'update', 'destroy']]);
        view()->share('app_section', 'time');
    }

    /**
     * Display a list of time entries
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = $request->user()->time()->with('project')->orderBy('start', 'desc')->simplePaginate(15);

        $q = $request->get('q');

        $viewVars = [
            'page_title' => 'Time',
            'q' => $q,
            'records' => $records,
            'search_route' => 'time.index'
        ];

        return view('time.list', $viewVars);

        //
    }

    /**
     * Show the form for creating a new time entry.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Save a new time entry to the database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing a time entry
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update an existing time entry
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Delete a time entry
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
