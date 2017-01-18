@extends('layouts.master')

@section('page_main')
    @include('partials.search', ['route' => 'projects.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Client</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td>
                        <a href="{{ route('projects.show', ['record' => $project]) }}">
                            {{ $project->name }}
                        </a>
                    </td>
                    <td>
                        {{ $project->status() }}
                    </td>
                    <td>
                        @if ($project->client)
                            {!! link_to_route('clients.show', $project->client->name, ['id' => $project->client->id]) !!}
                        @else
                            none
                        @endif
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($project->created_at) }}
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
    {!! link_to_route('projects.create', 'Add a project') !!}
@endsection

@section('page_scripts')
    @include('partials.vue')
    <script src="{{ asset('js/searchby.js') }}"></script>
@endsection
