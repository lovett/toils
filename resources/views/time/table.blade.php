@if ($collection->isNotEmpty())
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
        @foreach ($collection as $time)
            <tr>
                <td>
                    <a href="{{ route('time.edit', $time) }}">
                        {{ TimeHelper::dateFromRaw($time->start) }}
                    </a>
                </td>
                <td>
                    {{ $time->start->format('g:i A') }}

                    @if ($time->end)
                        → {{ $time->end->format('g:i A') }}
                    @endif
                    <div class="small">{{ TimeHelper::hoursAndMinutes($time->minutes) }}</div>
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
@endif
