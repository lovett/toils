@extends('layouts.master')

@section('page_main')
    @include('partials.search', ['route' => $search_route, 'q' => $q])
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
            @foreach ($records as $record)
                <tr>
                    <td>
	                <a href="{{ route('project.show', ['record' => $record]) }}">
	                    {{ $record->name }}
	                </a>
                    </td>
                    <td>
                        @if ($record->client)
                            {!! link_to_route('client.show', $record->client->name, ['id' => $record->client->id]) !!}
                        @else
                            ?
                        @endif
                    </td>
                    <td>
	                {{ $record->created_at->format('Y-m-d') }}
                    </td>
                    <td>
                        {{ $record->status() }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <nav>
        {!! $records->render() !!}
    </nav>
@endsection

@section('nav_primary')
    {!! link_to_route('project.create', 'Add a project') !!}
@endsection
