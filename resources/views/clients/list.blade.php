@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'client.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Projects</th>
                <th>Created</th>
                <th>Last Active</th>
                <th>Status</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>
	                <a href="{{ route('client.show', ['record' => $client]) }}">
	                    {{ $client->name }}
	                </a>
                    </td>
                    <td>
                        {{ $client->projectCount }}
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($client->created_at) }}
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($client->latestTime) }}
                    </td>
                    <td>
                        {{ $client->status() }}
                    </td>
                    <td>
                        {{ TimeHelper::hoursAndMinutes($client->totalTime) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <nav>
        {!! $clients->render() !!}
    </nav>
@endsection

@section('nav_primary')
    {!! link_to_route('client.create', 'Add a client') !!}
@endsection
