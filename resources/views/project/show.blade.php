@extends('layouts.app')

@section('page_main')
    <div class="container">
        <h1>{{ $project->name }}</h1>
        <p>@include('partials.active', ['value' => $project->active])</p>

        <div class="row">
	        <div class="col-sm-3">
	            <div class="well">
		            <dl>
                        <dt>Billable</dt>
                        <dd>{{ $project->billableStatus }}</dd>

		                <dt>Client</dt>
		                <dd>{!! link_to_route('client.show', $project->client->name, ['client' => $project->client]) !!}</dd>

		                <dt>Total Time</dt>
		                <dd>{{ TimeHelper::hoursAndMinutes($totalTime) }}</dd>

                        @if ($project->billable)
		                    <dt>Taxes</dt>
		                    <dd>{{ $project->taxStatus }}</dd>

		                    <dt>Total Money</dt>
		                    <dd>
                                {{ CurrencyHelper::money($totalMoney) }}
                                @if ($totalUnpaidMoney > 0)
                                    <small>plus {{ CurrencyHelper::money($totalUnpaidMoney) }} unpaid</small>
                                @endif
                            </dd>

		                    <dt>Hourly Rate</dt>
		                    <dd>{{ CurrencyHelper::hourlyRate($totalMoney, $totalTime) }}</dd>

                            @if ($totalTimeRemaining)
                                <dt>Time Remaining</dt>
                                <dd>{{ TimeHelper::hoursAndMinutes($totalTimeRemaining) }}</dd>
                            @endif

                            @if ($weeklyTimeRemaining)
                                <dt>Time Remining This Week</dt>
                                <dd>{{ TimeHelper::hoursAndMinutes($weeklyTimeRemaining) }}</dd>
                            @endif

                        @endif
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

        <h2>Time</h2>
        @include('partials.empty-message', ['collection' => $time])

        @if ($time->isNotEmpty())
            <div class="panel panel-default">
                @include('time.table', ['collection' => $time, 'project' => $project])
            </div>
        @endif

        <h2>Recent Invoices</h2>

        @include('partials.empty-message', ['collection' => $invoices])

        @if ($invoices->isNotEmpty())
            <div class="panel panel-default">
                @include('invoice.table', ['collection' => $invoices])
            </div>
        @endif

    </div>
    @include('partials.timestamps-footer', ['record' => $project])
@endsection

@section('subnav_supplemental')
	<li>{!! link_to_route('time.create', 'Add Time', ['project' => $project->id]) !!}</li>
@endsection
