@extends('layouts.master')

@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'search' => $search])
    <table class="table">
        <thead>
            <tr>
                <th width="200">Date</th>
                <th width="250">Project</th>
		<th>Summary</th>
		<th class="text-right">Accuracy</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($times as $time)
            <tr>
                <td>
                    {{ $time->start->format('Y-m-d, l') }}
		    <div class="small">
			{!! link_to_route('time.edit', $time->start->format('g:i A'), ['id' => $time->id]) !!}

			@if ($time->end)
			    to {{ $time->end->format('g:i A') }}
			@endif
		    </div>
                </td>
		<td>
	            {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}
                </td>

		<td>
		    {{ str_limit($time->summary, 75) }}
		</td>

                </td>
		<td class="text-right">
		    @if ($time->accuracy)
			{{ $time->accuracy }}%
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
