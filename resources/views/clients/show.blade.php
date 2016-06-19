@extends('layouts.master')

@section('page_main')
    <div class="row">
	<div class="col-sm-4">
	    <h2>Contact</h2>
	    <div class="well">
		<h4>{{ $record->contact_name }}</h4>
		<address>
		    <a href="mailto:{{ $record->contact_email }}">{{ $record->contact_email }}</a>
		    <div>
			{{ $record->address1 }}
			{{ $record->address2 }}
			{{ $record->city }}, {{ $record->locality }} {{ $record->postal_code }}
			<a href="tel://{{ $record->numericPhone() }}">{{ $record->phone }}</a>
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
			    @if ($record->projects->where('active', true)->count() == 0)
				<li>None</li>
			    @endif

			    @foreach ($record->projects->where('active', true) as $project)
				<li>
				    <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
				</li>
			    @endforeach
			</ul>
		    </div>
		</div>
		<div class="col-sm-6">
		    <div class="well">
			<h4>Inactive</h4>
			<ul class="list-unstyled">
			    @if ($record->projects->where('active', false)->count() == 0)
				<li>None</li>
			    @endif

			    @foreach ($record->projects->where('active', false) as $project)
				<li>
				    <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
				</li>
			    @endforeach
			</ul>
		    </div>
		</div>
	    </div>
	</div>
    </div>

    @include('partials.timestamps-footer', ['record' => $record])
@endsection

@section('nav_primary')
    <ul class="list-inline">
	<li>{!! link_to_route('client.edit', 'Edit', ['id' => $record->id]) !!}</li>
	<li>{!! link_to_route('project.create', 'New project') !!}</li>
    </ul>
@endsection
