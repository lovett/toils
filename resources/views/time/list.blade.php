@extends('layouts.master')

@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'search' => $search])
    <table class="table">
        <thead>
            <tr>
		<th>ID</th>
                <th width="200">Date</th>
		<th>Summary</th>
                <th width="250">Project</th>
		<th class="text-right">Accuracy</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($times as $time)
            <tr>
                <td>
		    {!! link_to_route('time.edit', $time->id, ['id' => $time->id]) !!}
		</td>
		<td>
                    {{ $time->start->format('Y-m-d, l') }}
		    <div class="small">
			{{ $time->start->format('g:i A') }}

			@if ($time->end)
			    to {{ $time->end->format('g:i A') }}
			@endif
		    </div>
                </td>

		<td>
		    {{ str_limit($time->summary, 75) }}
		</td>

		<td>
	            {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}
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
