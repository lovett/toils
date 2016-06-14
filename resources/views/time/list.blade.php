@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'q' => $q])
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Start</th>
                <th>End</th>
                <th>Project</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $record)
            @include('partials.time', ['record' => $record])
            @endforeach
        </tbody>
    </table>
    <nav>
        {!! $records->render() !!}
    </nav>
@endsection

@section('nav_primary')
    {!! link_to_route('time.create', 'Add time') !!}
@endsection
