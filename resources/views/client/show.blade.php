@extends('layouts.app')

@section('page_main')
    <div class="container">
        <h1>{{ $model->name }}</h1>
        <p>@include('partials.active', ['value' => $model->active])</p>

        <div class="card mb-5">
            <div class="card-body row">
                <div class="col-sm-6 col-md-4">
                    <dl>
                        <dt>Unpaid invoices</dt>
                        <dd>{{ CurrencyHelper::money($stats['unpaid']) }}</dd>
                        <dt>Total money</dt>
                        <dd>{{ CurrencyHelper::money($stats['total_money']) }}</dd>
                        <dt>Age</dt>
                        <dd>{{ $stats['age'] }}</dt>
                    </dl>
                </div>
                <div class="col-sm-6 col-md-4">
                    @if ($model->contactEmail)
                        <p><a href="mailto:{{ $model->contactEmail }}">{{ $model->contactEmail }}</a></p>
                    @endif

                    @if ($model->phone)
                        <p>{!! AddressHelper::phoneUrl($model->phone) !!}</p>
                    @endif

                    @if ($model->contactName)
                        <address>{{ AddressHelper::clientContact($model) }}</address>
                    @endif
                </div>
                <div class="col-sm-12 col-md-4">
                    Active projects:
                    <p>
                        @foreach ($model->projects->where('active', true) as $project)
                            <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
                        @endforeach
                    </p>

                    Inactive projects:
                    <p>
                        @foreach ($model->projects->where('active', false) as $project)
                            <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
                        @endforeach
                    </p>


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

    @include('partials.timestamps-footer', ['record' => $model])
@endsection

@section('subnav_supplemental')
    <li class="nav-item">{!! link_to_route('project.create', 'New project', ['client' => $model->id], ['class' => 'nav-link']) !!}</li>
    <li class="nav-item">{!! link_to_route('time.create', 'New time entry', ['client' => $model->id], ['class' => 'nav-link']) !!}</li>
    <li class="nav-item">{!! link_to_route('invoice.create', 'New invoice', ['client' => $model->id], ['class' => 'nav-link']) !!}</li>
@endsection
