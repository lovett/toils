@extends('layouts.master')

@section('page_main')
    <div class="row">
	<div class="col-sm-4">
	    <h2>Contact</h2>
	    <div class="well">
		<h4>{{ $client->contact_name }}</h4>
		<address>
		    <a href="mailto:{{ $client->contact_email }}">{{ $client->contact_email }}</a>
		    <div>
			{{ $client->address1 }}
			{{ $client->address2 }}
			{{ $client->city }}, {{ $client->locality }} {{ $client->postal_code }}
			<a href="tel://{{ $client->numericPhone() }}">{{ $client->phone }}</a>
		    </div>
		</address>
	    </div>
	</div>
	<div class="col-sm-8">

	    <h2>Projects</h2>

	    <div class="row">
		<div class="col-sm-6">
		    <div class="well">
			<h4>Active</h4>
			<ul class="list-unstyled">
			    @if ($client->projects->where('active', true)->count() == 0)
				<li>None</li>
			    @endif

			    @foreach ($client->projects->where('active', true) as $project)
				<li>
				    <a href="{{ route('project.edit', ['project' => $project]) }}">{{ $project->name }}</a>
				</li>
			    @endforeach
			</ul>
		    </div>
		</div>
		<div class="col-sm-6">
		    <div class="well">
			<h4>Inactive</h4>
			<ul class="list-unstyled">
			    @if ($client->projects->where('active', false)->count() == 0)
				<li>None</li>
			    @endif

			    @foreach ($client->projects->where('active', false) as $project)
				<li>
				    <a href="{{ route('project.edit', ['project' => $project]) }}">{{ $project->name }}</a>
				</li>
			    @endforeach
			</ul>
		    </div>
		</div>
	    </div>
	</div>
    </div>
    <div class="row">
	<div class="col-sm-12">
	    Updated {{ $client->updated_at->format('Y-m-d \a\t g:i A') }}.
	    Created {{ $client->created_at->format('Y-m-d') }}.
	</div>
    </div>
@endsection

@section('nav_primary')
    <ul class="list-inline">
	<li>{!! link_to_route('client.edit', 'Edit', ['id' => $client->id]) !!}</li>
	<li>{!! link_to_route('project.create', 'New project') !!}</li>
    </ul>
@endsection
