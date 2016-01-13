@extends('layouts.master')

@section('page_main')
<div class="row">
    <div class="col-sm-3">
	<h2>Contact</h2>
	<nav class="subhead-nav">
	    <a href="{{ route('client.edit', ['client' => $client ]) }}" role="button" class="btn btn-primary btn-xs">Edit</a>
	</nav>

	<div class="well" >
	    <a href="mailto:{{ $client->contact_email }}">{{ $client->contact_name }}</a>
	    <address>
		{{ $client->address1 }}
		{{ $client->address2 }}
		{{ $client->city }}, {{ $client->locality }} {{ $client->postal_code }}
		<a href="tel://{{ $client->numericPhone() }}">{{ $client->phone }}</a>
	    </address>
	</div>
    </div>
    <div class="col-sm-3">
	<h2>Projects</h2>

	<nav class="subhead-nav">
	    <a href="{{ route('project.create') }}" role="button" class="btn btn-primary btn-xs">Add</a>
	</nav>

	<div class="well">
	    <ul class="list-unstyled">
	    @if ($client->projects->count() == 0)
	    <li>None</li>
	    @endif

	    @foreach ($client->projects as $project)
	    <li><a href="{{ route('project.edit', ['project' => $project]) }}">{{ $project->name }}</a></li>
	    @endforeach
	    </ul>
	</div>
    </div>
</div>

@endsection

@section('nav_primary')
        {!! link_to_route('clients', 'All clients') !!}
@endsection

@section('nav_supplemental')
@endsection
