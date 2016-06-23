@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'q' => $q])
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
            @foreach ($records as $record)
            <tr>
                <td>
                    {{ $record->start->format('Y-m-d, l') }}
                </td>
                <td>
                    {!! link_to_route('time.edit', $record->start->format('g:i A'), ['id' => $record->id]) !!}
                </td>
                <td>
	            {{ $record->end->format('g:i A') }}
                </td>
                <td>
	            {!! link_to_route('project.show', $record->project->name, ['id' => $record->project->id]) !!}
                </td>
                <td class="text-right">
                    {{ TimeHelper::hoursAndMinutes($record->minutes) }}
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
    {!! link_to_route('time.create', 'Add time') !!}
@endsection
