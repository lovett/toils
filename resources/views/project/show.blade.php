@extends('layouts.app')

@section('page_main')
    <div class="container">
        <div class="mb-4">
            <h1>{{ $project->name }}</h1>
            <ul class="list-group list-group-horizontal-sm">
                <li class="list-group-item">@include('partials.active', ['value' => $project->active])</li>
                <li class="list-group-item">@include('partials.billable', ['value' => $project->billable])</li>
                <li class="list-group-item">@include('partials.taxable', ['value' => $project->taxable])</li>
            </ul>
        </div>

        <div class="row mb-5">
	        <div class="col-sm-3">
	            <div class="card">
                    <div class="card-body">
		                <dl>
                            <dt>Active</dt>
                            <dd>
                                @if ($stats['end'])
                                    for {{ $stats['duration'] }}
                                    <p class="small">
                                        <span class="text-nowrap">{{ TimeHelper::longDate($stats['start']) }}</span> to
                                        <span class="text-nowrap">{{ TimeHelper::longDate($stats['end']) }}</span>
                                    </p>
                                @else
                                    for {{ $stats['age'] }}
                                    <p class="small">
                                        since <span class="text-nowrap">{{ TimeHelper::longDate($stats['start']) }}</span>
                                    </p>
                                @endif
                            </dd>

		                    <dt>Client</dt>
		                    <dd>{!! link_to_route('client.show', $project->client->name, ['client' => $project->client]) !!}</dd>

		                    <dt>Billable Time</dt>
		                    <dd>{{ TimeHelper::hoursAndMinutes($stats['billable_minutes']) }}</dd>

		                    <dt>Unbillable Time</dt>
		                    <dd>{{ TimeHelper::hoursAndMinutes($stats['unbillable_minutes']) }}</dd>

                            @if ($project->billable)
		                        <dt>Income</dt>
		                        <dd>
                                    {{ CurrencyHelper::dollars($stats['income']) }}
                                    @if ($stats['unpaid'] > 0)
                                        <p class="small">plus {{ CurrencyHelper::dollars($stats['unpaid']) }} unpaid</p>
                                    @endif
                                </dd>

		                        <dt>Hourly Rate</dt>
		                        <dd>{{ CurrencyHelper::money($stats['hourly_rate']) }}</dd>

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
