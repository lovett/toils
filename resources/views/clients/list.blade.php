@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'client.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Client</th>
                <th>Status</th>
                <th>Projects</th>
                <th>Total Time</th>
                <th>Last Active</th>
                <th>Started</th>
                <th>Created</th>
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
                        {{ $client->status() }}
                    </td>
                    <td>
                        {{ $client->projectCount }}
                    </td>
                    <td>
                        {{ TimeHelper::hoursAndMinutes($client->totalTime) }}
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($client->latestTime, 'never')}}
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($client->earliestTime, 'never')}}
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($client->created_at) }}
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

@section('page_scripts')
    <script src="{{ asset('js/vue.min.js') }}"></script>
    <script src="{{ asset('js/searchby.js') }}"></script>
@endsection
