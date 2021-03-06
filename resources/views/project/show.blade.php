@extends('layouts.app')

@section('subnav')
    <div class="container mb-4">
        <ul class="nav nav-tabs flex-column flex-lg-row">
            <li class="nav-item">
                <a class="nav-link disabled" href="#">Overview</a>
            </li>

	        <li class="nav-item">
                @unless ($time->isEmpty())
                    <a class="nav-link" href="#time">Time</a>
                @endunless
            </li>

	        <li class="nav-item">
                @unless ($invoices->isEmpty())
                    <a class="nav-link" href="#invoices">Invoices</a>
                @endunless
            </li>
	        <li class="nav-item">
                {!! link_to_route('invoice.create', 'New Invoice', ['project' => $project->id], ['class' => 'nav-link']) !!}
            </li>
        </ul>
    </div>
@endsection

@section('page_main')
    <div class="container">
        <div class="mb-4">
            <h1>{{ $project->name }}</h1>
            <ul class="list-group list-group-horizontal-sm">
                <li class="list-group-item">
                    @include('partials.active', ['value' => $project->active])
                </li>

                <li class="list-group-item">
                    @include('partials.billable', ['value' => $project->billable])
                </li>

                @if ($project->billable)
                <li class="list-group-item">
                    @include('partials.taxable', ['value' => $project->taxable])
                </li>
                @endif

                <li class="list-group-item">
                    @include('partials.edit', ['value' => 'edit', 'route' => 'project.edit', 'parameters' => ['project' => $project->id]])
                </li>
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
                                        <span class="text-nowrap">{{ TimeHelper::longDate($timezone, $stats['start']) }}</span> to
                                        <span class="text-nowrap">{{ TimeHelper::longDate($timezone, $stats['end']) }}</span>
                                    </p>
                                @else
                                    for {{ $stats['age'] }}
                                    <p class="small">
                                        since <span class="text-nowrap">{{ TimeHelper::longDate($timezone, $stats['start']) }}</span>
                                    </p>
                                @endif
                            </dd>

		                    <dt>Client</dt>
		                    <dd>{!! link_to_route('client.show', $project->client->name, ['client' => $project->client]) !!}</dd>

                            @if ($project->billable)
		                    <dt>Billable Time</dt>
		                    <dd>{{ TimeHelper::hoursAndMinutes($stats['billable_minutes']) }}</dd>
                            @endif

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
			                {{ TimeHelper::hoursAndMinutes($weekSliceTotal) }}
                            in the past {{ $weekSliceRange }} {{ str_plural('week', $weekSliceRange) }}
		                </h2>
		                @foreach ($weekSlice as $date => $totalMinutes)
			                <p>
			                    {{ $date }} - {{ TimeHelper::minutesToHours($totalMinutes) }}
			                </p>
		                @endforeach
		            </div>
	            </div>
	            <div class="card">
		            <div class="card-body">
		                <h2 class="card-title">
			                {{ TimeHelper::hoursAndMinutes($monthSliceTotal) }}
                            in the past {{ $monthSliceRange }} {{ str_plural('month', $monthSliceRange) }}
		                </h2>
		                @foreach ($monthSlice as $date => $totalMinutes)
			                <p>
			                    {{ $date }} - {{ TimeHelper::minutesToHours($totalMinutes) }}
			                </p>
		                @endforeach
		            </div>
	            </div>
	        </div>
        </div>

        @unless ($time->isEmpty())
            <div class="mb-5">
                <h2 id="time">Time</h2>

                <ul class="nav">
                    <li class="nav-item">
                        {!! link_to_route('time.create', 'New', ['project' => $project->id], ['class' => 'nav-link active']) !!}</li>
                    <li class="nav-item">
                        {!! link_to_route('time.index', 'View all', ['q' => 'project:' . $project->name], ['class' => 'nav-link active']) !!}</li>
                </ul>

                <div class="card">
                    @include('time.table', ['collection' => $time, 'project' => $project])
                </div>
            </div>
        @endunless

        @unless ($invoices->isEmpty())
            <div class="mb-5">
                <h2 id="invoices">Invoices</h2>

                <ul class="nav">
                    <li class="nav-item">
                        {!! link_to_route('invoice.create', 'New', ['project' => $project->id], ['class' => 'nav-link']) !!}</li>
                    <li class="nav-item">
                        {!! link_to_route('invoice.index', 'View all', ['q' => 'project:' . $project->name], ['class' => 'nav-link']) !!}
                    </li>
                </ul>



                <div class="card mb-5">
                    @include('invoice.table', ['collection' => $invoices])
                </div>
            </div>
        @endunless
    </div>
@endsection
