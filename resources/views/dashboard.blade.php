@extends('layouts.app')

@section('page_main')
<div class="container">

    @if ($unfinishedTime->isNotEmpty())
    <div class="row mb-4">
        <div class="col-md-10 offset-md-1">
            <h2>In Progress</h2>
            <div class="card">
                @include('time.table', ['collection' => $unfinishedTime])
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-10 offset-md-1">
            <h2>Active Projects</h2>
            <p>{{ TimeHelper::hoursAndMinutes($totalUnbilled) }} of billable time.</p>

            <div class="row">
                @foreach ($activeProjects as $project)
                <div class="col-md-6 col-lg-4 card-group">
                    <div class="card mb-3">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title flex-fill">{!! link_to_route('project.show', $project->name, ['id' => $project->id]) !!}</h5>
                            <p>{{ TimeHelper::hoursAndMinutes($project->unbilledTime) }}</p>

                            <div>
                                {!! LinkHelper::cardLink('time.create', 'time', ['project' => $project->id]) !!}
                                {!! LinkHelper::cardLink('invoice.create', 'invoice', ['project' => $project->id]) !!}
                            </div>
                        </div>

                        <div class="card-footer text-muted">
                            active {{ TimeHelper::daysAgo($project->updated_at) }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
