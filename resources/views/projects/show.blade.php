@extends('layouts.master')

@section('page_main')
    <div class="row">
	<div class="col-sm-3">
	    <div class="well">
		<dl class="spaced">
		    <dt>Status</dt>
		    <dd>{{ $record->status() }}</dd>

		    <dt>Taxes</dt>
		    <dd>{{ $record->taxStatus() }}</dd>

		    <dt>Client</dt>
		    <dd>{!! link_to_route('client.show', $record->client->name, ['client' => $record->client]) !!}</dd>

		</dl>

	    </div>
	</div>

    </div>

    <div class="row">
	<div class="col-sm-12">
	    Updated {{ $record->updated_at->format('Y-m-d \a\t g:i A') }}.
	    Created {{ $record->created_at->format('Y-m-d') }}.
	</div>
    </div>
@endsection

@section('nav_primary')
    <ul class="list-inline">
	<li>{!! link_to_route('project.edit', 'Edit', ['id' => $record->id]) !!}</li>
	<li>{!! link_to_route('project.create', 'New project') !!}</li>
    </ul>
@endsection
