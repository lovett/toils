@extends('layouts.master')

@section('page_main')
    @include('partials.search', ['route' => $searchRoute, 'q' => $q])
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Client</th>
                <th>Created</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>
	                <a href="{{ route('project.show', ['record' => $project]) }}">
	                    {{ $project->name }}
	                </a>
                    </td>
                    <td>
                        @if ($project->client)
                            {!! link_to_route('client.show', $project->client->name, ['id' => $project->client->id]) !!}
                        @else
                            ?
                        @endif
                    </td>
                    <td>
	                {{ $project->created_at->format('Y-m-d') }}
                    </td>
                    <td>
                        {{ $project->status() }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <nav>
        {!! $projects->render() !!}
    </nav>
@endsection

@section('nav_primary')
    {!! link_to_route('project.create', 'Add a project') !!}
@endsection
