@extends('layouts.app')

@section('page_main')
    <div class="container">
        <h1>{{ $project->name }}</h1>
        <p>@include('partials.active', ['value' => $project->active])</p>

        <div class="row mb-5">
	        <div class="col-sm-3">
	            <div class="card">
                    <div class="card-body">
		                <dl>
                            <dt>Billable</dt>
                            <dd>{{ $project->billableStatus }}</dd>

		                    <dt>Client</dt>
		                    <dd>{!! link_to_route('client.show', $project->client->name, ['client' => $project->client]) !!}</dd>

		                    <dt>Billable Time</dt>
		                    <dd>{{ TimeHelper::hoursAndMinutes($billableMinutes) }}</dd>

		                    <dt>Unillable Time</dt>
		                    <dd>{{ TimeHelper::hoursAndMinutes($unbillableMinutes) }}</dd>

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
		                        <dd>{{ CurrencyHelper::hourlyRate($totalMoney, $totalMinutes) }}</dd>

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
	        </div>
	        <div class="col-sm-9">
	            <div class="card">
		            <div class="card-body">
		                <h2 class="card-title">
			                {{ TimeHelper::hoursAndMinutes($sliceTotal) }}
                            in the past {{ $sliceRange }} {{ str_plural('month', $sliceRange) }}
		                </h2>
		                @foreach ($slice as $date => $totalMinutes)
			                <p>
			                    {{ $date }} - {{ TimeHelper::minutesToHours($totalMinutes) }}
			                </p>
		                @endforeach
		            </div>
	            </div>
	        </div>
        </div>

        <div class="mb-5">
            <h2>Time</h2>

            @unless ($time->isEmpty())
            <p>{!! link_to_route('time.index', 'View all', ['q' => 'project:' . $project->name]) !!}</p>
            @endunless

            @include('partials.empty-message', ['collection' => $time, 'collectionOf' => 'time entries'])

            @if ($time->isNotEmpty())
                <div class="card">
                    @include('time.table', ['collection' => $time, 'project' => $project])
                </div>
            @endif
        </div>

        <div class="mb-5">
            <h2>Invoices</h2>

            @unless ($invoices->isEmpty())
                <p>{!! link_to_route('invoice.index', 'View all', ['q' => 'project:' . $project->name]) !!}</p>
            @endunless

            @include('partials.empty-message', ['collection' => $invoices, 'collectionOf' => 'invoices'])

            @if ($invoices->isNotEmpty())
                <div class="card mb-5">
                    @include('invoice.table', ['collection' => $invoices])
                </div>
            @endif
        </div>
    </div>
@endsection

@section('subnav_supplemental')
	<li class="nav-item">
        {!! link_to_route('time.create', 'New time entry', ['project' => $project->id], ['class' => 'nav-link']) !!}
    </li>
    <li class="nav-item">
        {!! link_to_route('invoice.create', 'New Invoice', ['project' => $project->id], ['class' => 'nav-link']) !!}
    </li>
@endsection
