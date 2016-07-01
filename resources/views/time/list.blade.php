@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'search' => $search])
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Project</th>
                <th class="text-right">Duration</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($times as $time)
            <tr>
                <td>
                    {{ $time->start->format('Y-m-d, l') }}
                </td>
                <td>
                    {!! link_to_route('time.edit', $time->start->format('g:i A'), ['id' => $time->id]) !!}
                </td>
                <td>
	            {{ $time->end->format('g:i A') }}
                </td>
                <td>
	            {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}
                </td>
                <td class="text-right">
                    {{ TimeHelper::hoursAndMinutes($time->minutes) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <nav>
        {!! $times->render() !!}
    </nav>
@endsection

@section('nav_primary')
    {!! link_to_route('time.create', 'Add time') !!}
@endsection
