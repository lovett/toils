@extends('layouts.app')

@section('page_main')
<div class="container">

    <div class="row mb-3">
        <div class="col-md-10 offset-md-1">
            <ul class="list-inline">
                <li class="list-inline-item">{!! LinkHelper::smallButtonLink('project.create', 'New project') !!}</li>
                <li class="list-inline-item">{!! LinkHelper::smallButtonLink('client.create', 'New client') !!}</li>
                <li class="list-inline-item">{!! LinkHelper::smallButtonLink('estimate.create', 'New estimate') !!}</li>
            </ul>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-10 offset-md-1">

            @if ($unfinishedTime->isNotEmpty())
                <h2>In Progress</h2>
                <div class="card">
                    @include('time.table', ['collection' => $unfinishedTime])
                </div>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-10 offset-md-1">
            <h2>Projects</h2>
            <p>{{ TimeHelper::hoursAndMinutes($totalUnbilled) }} of billable time.</p>
            <div class="grid">
                @foreach ($activeProjects as $project)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">{!! link_to_route('project.show', $project->name, ['id' => $project->id]) !!}</h5>
                            <p>{{ TimeHelper::hoursAndMinutes($project->unbilledTime) }}</p>

                            {!! LinkHelper::extraSmallButtonLink('time.create', 'time', ['project' => $project->id]) !!}
                            {!! LinkHelper::extraSmallButtonLink('invoice.create', 'invoice', ['project' => $project->id]) !!}
                        </div>

                        <div class="card-footer text-muted">
                            active {{ TimeHelper::daysAgo($project->updated_at) }}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection
