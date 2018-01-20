@extends('layouts.app')

@section('page_main')
    <div class="container">
        <div class="row">
	        <div class="col-sm-3">
	            <div class="well">
		            <dl>
		                <dt>Status</dt>
		                <dd>{{ $project->status() }}</dd>

		                <dt>Taxes</dt>
		                <dd>{{ $project->taxStatus() }}</dd>

		                <dt>Client</dt>
		                <dd>{!! link_to_route('client.show', $project->client->name, ['client' => $project->client]) !!}</dd>

		                <dt>Total Time</dt>
		                <dd>{{ TimeHelper::hoursAndMinutes($totalTime) }}</dd>
		            </dl>
	            </div>
	        </div>
	        <div class="col-sm-9">
	            <div class="panel panel-default">
		            <div class="panel-heading">
		                <h2 class="panel-title">
			                {{ TimeHelper::hoursAndMinutes($sliceTotal) }}
                            in the past {{ $sliceRange }} {{ str_plural('month', $sliceRange) }}
		                </h2>
		            </div>
		            <div class="panel-body">
		                @foreach ($slice as $date => $totalMinutes)
			                <p>
			                    {{ $date }} - {{ TimeHelper::minutesToHours($totalMinutes) }}
			                </p>
		                @endforeach
		            </div>
	            </div>
	        </div>
        </div>
    </div>
    @include('partials.timestamps-footer', ['record' => $project])
@endsection

@section('subnav_supplemental')
	<li>{!! link_to_route('time.create', 'Add Time', ['project' => $project->id]) !!}</li>
@endsection
