@if ($collection->isNotEmpty())
<table class="table">
    <thead>
        <tr>
            <th width="20%">Date</th>
            <th width="20%">Time</th>
            @unless(Route::is('project.show'))
                <th width="20%">Project</th>
            @endunless
            <th>Summary</th>
            <th class="text-right">Accuracy</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $time)
            <tr>
                <td>
                    <a href="{{ route('time.edit', $time) }}">
                        {{ TimeHelper::date($time->start) }}
                    </a>
                </td>
                <td>
                    {{ $time->start->format('g:i A') }}

                    @if ($time->end)
                        → {{ $time->end->format('g:i A') }}
                    @endif
                    <div class="small">{{ TimeHelper::hoursAndMinutes($time->minutes) }}</div>
                </td>

                @unless(Route::is('project.show'))
                <td>
                    {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}
                </td>
                @endunless

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
@endif
