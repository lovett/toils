@extends('layouts.app')

@section('page_main')
    <div class="container">
        <div class="mb-4">
            <h1>{{ $model->name }}</h1>
            <ul class="list-group list-group-horizontal-sm">
                <li class="list-group-item">@include('partials.active', ['value' => $model->active])</li>
            </ul>
        </div>

        <div class="row mb-5">
	        <div class="col-sm-3">
	            <div class="card">
                    <div class="card-body">
                        <dl>
                            <dt>Unpaid invoices</dt>
                            <dd>{{ CurrencyHelper::money($stats['unpaid']) }}</dd>
                            <dt>Income</dt>
                            <dd>{{ CurrencyHelper::money($stats['income']) }}</dd>
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

                            @if ($model->contactEmail)
                                <dt>Email</dt>
                                <dd>
                                    <a href="mailto:{{ $model->contactEmail }}">{{ $model->contactEmail }}</a>
                                </dd>
                            @endif

                            @if ($model->phone)
                                <dt>Phone</dt>
                                <dd>{!! AddressHelper::phoneUrl($model->phone) !!}</dd>
                            @endif

                            @if ($model->contactName)
                                <dt>Address</dt>
                                <dd><address>{{ AddressHelper::clientContact($model) }}</address></dd>
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
        <div class="mb-5">
            <h2>Projects</h2>

            <div class="card">
                <div class="card-body">
                    <ul class="list-unstyled mb-0 columnwise">
                        @if ($model->projects->isEmpty())
                            <li>None</li>
                        @endif

                        @foreach ($model->projects->where('active', true) as $project)
                            <li class="indent-for-hanging-icon">
                                <svg class="icon active"><use xlink:href="#icon-checkmark"></use></svg>
                                <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
                            </li>
                        @endforeach

                        @foreach ($model->projects->where('active', false) as $project)
                            <li class="indent-for-hanging-icon">
                                <svg class="icon inactive"><use xlink:href="#icon-blocked"></use></svg>
                                <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h2>Time</h2>

            @unless ($time->isEmpty())
                <p>{!! link_to_route('time.index', 'View all', ['q' => 'client:' . $model->name]) !!}</p>
            @endif

            @include('partials.empty-message', ['collection' => $time, 'collectionOf' => 'time entries'])

            @if ($time->isNotEmpty())
                <div class="card mb-5">
                    @include('time.table', ['collection' => $time])
                </div>
            @endif
        </div>

        <div class="mb-5">
            <h2>Invoices</h2>

            @unless ($invoices->isEmpty())
                <p>{!! link_to_route('invoice.index', 'View all', ['q' => 'client:' . $model->name]) !!}</p>
            @endunless

            @include('partials.empty-message', ['collection' => $invoices, 'collectionOf' => 'invoices'])

            @if ($invoices->isNotEmpty())
                <div class="card pb-0">
                    @include('invoice.table', ['collection' => $invoices])
                </div>
            @endif
        </div>

        <div class="mb-5">
            <h2>Estimates</h2>

            @unless ($time->isEmpty())
                <p>{!! link_to_route('estimate.index', 'View all', ['q' => 'client:' . $model->name]) !!}</p>
            @endunless

            @include('partials.empty-message', ['collection' => $estimates, 'collectionOf' => 'estimates'])

            @if ($estimates->isNotEmpty())
                <div class="card mb-5">
                    @include('estimate.table', ['collection' => $estimates])
                </div>
            @endif
        </div>
    </div>
@endsection

@section('subnav_supplemental')
    <li class="nav-item">{!! link_to_route('project.create', 'New project', ['client' => $model->id], ['class' => 'nav-link']) !!}</li>
    <li class="nav-item">{!! link_to_route('time.create', 'New time entry', ['client' => $model->id], ['class' => 'nav-link']) !!}</li>
    <li class="nav-item">{!! link_to_route('invoice.create', 'New invoice', ['client' => $model->id], ['class' => 'nav-link']) !!}</li>
@endsection
