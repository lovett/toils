@extends('layouts.master')

@section('page_main')
    <div class="row">
    <div class="col-sm-4">
        <h2>Contact</h2>
        <div class="well">
        @if (empty($model->contactName))
            <p>Not specified</p>
        @else
        <h4>{{ $model->contactName }}</h4>
        <address>
            <a href="mailto:{{ $model->contactEmail }}">{{ $model->contactEmail }}</a>
            <div>
            {{ AddressHelper::mailingAddress($model) }}
            {!! AddressHelper::phoneUrl($model->phone) !!}
            </div>
        </address>
        @endif
        </div>
    </div>
    <div class="col-sm-8">

        <h2>Projects</h2>

        <div class="row">
        <div class="col-sm-6">
            <div class="well">
            <h4>Active</h4>
            <ul class="list-unstyled">
                @if ($model->projects->where('active', true)->count() == 0)
                <li>None</li>
                @endif

                @foreach ($model->projects->where('active', true) as $project)
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
                @if ($model->projects->where('active', false)->count() == 0)
                <li>None</li>
                @endif

                @foreach ($model->projects->where('active', false) as $project)
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

    @include('partials.timestamps-footer', ['record' => $model])
@endsection

@section('nav_primary')
    <ul class="list-inline">
    <li>{!! link_to_route('client.edit', 'Edit', ['id' => $model->id]) !!}</li>
    <li>{!! link_to_route('project.create', 'New project', ['client' => $model->id]) !!}</li>
    </ul>
@endsection
