@extends('layouts.app')

@section('page_main')
<div class="container">

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ul class="list-inline">
                <li>{!! LinkHelper::buttonLink('project.create', 'New project') !!}</li>
                <li>{!! LinkHelper::buttonLink('client.create', 'New client') !!}</li>
                <li>{!! LinkHelper::buttonLink('estimate.create', 'New estimate') !!}</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">


            <h2>Projects</h2>
            <p>{{ TimeHelper::hoursAndMinutes($totalUnbilled) }} of billable time.</p>
            <div class="grid">
                @foreach ($activeProjects as $project)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {!! link_to_route('project.show', $project->name, ['id' => $project->id]) !!}
                        </div>

                        <div class="panel-body">
                            <p>{{ TimeHelper::hoursAndMinutes($project->unbilledTime) }}</p>
                            <p>active {{ TimeHelper::daysAgo($project->updated_at) }}</p>
                        </div>

                        <div class="panel-footer grid ">
                            {!! link_to_route('time.create', 'time', ['project' => $project->id]) !!}
                            {!! link_to_route('invoice.create', 'invoice', ['project' => $project->id]) !!}
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>
@endsection
