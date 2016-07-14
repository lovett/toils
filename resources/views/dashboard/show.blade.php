@extends('layouts.master')

@section('page_main')
    <div class="row">
	<div class="col-sm-4">
	    <div class="well">
		Hello from the dashboard.
	    </div>
	</div>
    </div>
@endsection

@section('nav_primary')
    <ul class="list-inline">
	<li>{!! link_to_route('time.create', 'Add time') !!}</li>
    </ul>
@endsection
