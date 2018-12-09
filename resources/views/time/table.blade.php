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
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $time)
            @php($unfinishedClass = ($time->minutes === 0)? 'table-warning' : '')
            @php($billableClass = ($time->billable)? '' : 'unbillable')
            <tr class="{{ $unfinishedClass }} {{ $billableClass }}">
                <td>
                    <a href="{{ route('time.edit', $time) }}">
                        {{ TimeHelper::date($time->start) }}
                    </a>
                </td>
                <td>
                    {{ $time->start->format('g:i A') }}
                    →
                    @if ($time->end)
                        {{ $time->end->format('g:i A') }}
                        <div class="small">{{ TimeHelper::hoursAndMinutes($time->minutes) }}</div>
                    @else
                        ?

                        {!! Form::model($time, ['route' => ['time.finish'], 'method' => 'POST']) !!}
                        {!! Form::hidden('id', $time->id) !!}
                        {!! Form::button('FINISH', ['type' => 'submit', 'class' => 'btn finish btn-info btn-sm']) !!}
                        {!! Form::close() !!}
                    @endif
                </td>

                @unless(Route::is('project.show'))
                    <td>
                        {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}

                        @if (Route::is('time.index'))
                            <p>
                                <small>
                                    {!! link_to_route('client.show', $time->client_name, ['id' => $time->client_id]) !!}
                                </small>
                            </p>
                        @endif

                </td>
                @endunless

                <td>
                    {{ str_limit($time->summary, 75) }}

                    @if (count($time->tags) > 0)
                        <p>
                            @foreach ($time->tags as $tag)
                                <span class="tag">
                                    <svg class="icon">
                                        <use xlink:href="#icon-price-tag" />
                                    </svg>
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </p>
                    @endif
                </td>

                </td>
                <td class="text-right">
                    @if ($time->accuracy)
                        {{ $time->accuracy }}%
                    @endif
                </td>
                <td class="text-center">
                    @unless ($time->billable)
                        <svg class="icon inactive"><use xlink:href="#icon-blocked" /></svg>
                    @endunless

                    @if ($time->invoice_id)
                        <svg class="icon active"><use xlink:href="#icon-checkmark" /></svg>
                    @endif
                </td>

            </tr>
        @endforeach
    </tbody>
</table>
@endif
