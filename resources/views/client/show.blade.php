@extends('layouts.app')

@section('page_main')
    <div class="container">
        <h1>{{ $model->name }}</h1>
        <p>@include('partials.active', ['value' => $model->active])</p>

        <div class="row mb-5">
	        <div class="col-sm-3">
	            <div class="card">
                    <div class="card-body">
                        <dl>
                            <dt>Unpaid invoices</dt>
                            <dd>{{ CurrencyHelper::money($stats['unpaid']) }}</dd>
                            <dt>Total money</dt>
                            <dd>{{ CurrencyHelper::money($stats['total_money']) }}</dd>
                            <dt>Start</dt>
                            <dd>
                                {{ TimeHelper::longDate($stats['start']) }}
                                <p class="small">
                                    {{ $stats['age'] }}
                                </p>
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
            <h2>Projects</h2>

            <div class="card mb-5">
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-2">
                            @include('partials.active', ['value' => true])
                        </dt>
                        <dd class="col-3">
                            <ul class="list-unstyled">
                            @foreach ($model->projects->where('active', true) as $project)
                                <li><a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a></li>
                            @endforeach
                            </ul>
                        </dd>

                        <dt class="col-2">
                            @include('partials.active', ['value' => false])
                        </dt>
                        <dd class="col-3">
                            <ul class="list-unstyled">
                                @foreach ($model->projects->where('active', false) as $project)
                                    <li><a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a></li>
                                @endforeach
                            </ul>
                        </dd>
                    </dl>
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
