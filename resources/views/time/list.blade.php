@extends('layouts.master')

@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table">
        <thead>
            <tr>
                <th width="20%">Date</th>
                <th width="20%">Time</th>
                <th width="20%">Project</th>
                <th>Summary</th>
                <th class="text-right">Accuracy</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($times as $time)
                <tr>
                    <td>
                        <a href="{{ route('time.edit', $time) }}">
                            {{ TimeHelper::dateFromRaw($time->start) }}
                            <div class="small">
                                {{ $time->start->format('g:i A') }}

                                @if ($time->end)
                                    to {{ $time->end->format('g:i A') }}
                                @endif
                            </div>
                        </a>
                    </td>
                    <td>
                        {{ TimeHelper::hoursAndMinutes($time->minutes) }}
                    </td>

                    <td>
                        {!! link_to_route('projects.show', $time->project->name, ['id' => $time->project->id]) !!}
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

@section('page_scripts')
    @include('partials.vue')
    <script src="{{ asset('js/searchby.js') }}"></script>
@endsection
