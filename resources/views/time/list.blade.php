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
		<th class="text-right">Accuracy</th>
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
		    @if ($time->end)
			{{ $time->end->format('g:i A') }}
		    @endif

		    @if (empty($time->end))
			&nbsp;
		    @endif
                </td>
                <td>
	            {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}
                </td>
                <td class="text-right">
                    {{ TimeHelper::hoursAndMinutes($time->minutes) }}
		    @if ($time->estimatedDuration)
			<div class="small">Estimate: {{ TimeHelper::hoursAndMinutes($time->estimatedDuration) }}</div>
		    @else
			&nbsp;
		    @endif
		</td>
		<td class="text-right">
		    @if ($time->accuracy)
			{{ $time->accuracy }}%
		    @else
			&nbsp;
		    @endif
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
