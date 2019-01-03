@if ($collection->isNotEmpty())
<table class="table">
    <thead>
        <tr>
            <th width="20%">Time</th>
            <th width="20%">Date</th>
            @unless(Route::is('project.show'))
                <th width="20%">Project</th>
            @endunless
            <th>Summary</th>

            @unless(Route::is('dashboard'))
                <th class="text-right">Accuracy</th>
            @endunless

            @unless(Route::is('dashboard'))
                <th>Status</th>
            @endunless
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $time)
            @php($unfinishedClass = ($time->minutes === 0)? 'table-warning' : '')
            @php($billableClass = ($time->billable)? '' : 'unbillable')
            <tr class="{{ $unfinishedClass }} {{ $billableClass }}">
                <td>
                    <a href="{{ route('time.edit', $time) }}">
                        {{ TimeHelper::time($time->start) }}
                        →
                        @if ($time->end)
                            {{ TimeHelper::time($time->end) }}
                        @else
                            ?
                        @endif
                    </a>

                    @if ($time->end)
                        <div class="small">{{ TimeHelper::hoursAndMinutes($time->minutes) }}</div>
                    @else
                        {!! Form::model($time, ['route' => ['time.finish'], 'method' => 'POST']) !!}
                        {!! Form::hidden('id', $time->id) !!}
                        {!! Form::button('FINISH', ['type' => 'submit', 'class' => 'btn finish btn-info btn-sm']) !!}
                        {!! Form::close() !!}
                    @endunless
                </td>
                <td>
                        {{ TimeHelper::date($time->start) }}
                    </a>
                </td>

                @unless(Route::is('project.show'))
                    <td>
                        {!! link_to_route('project.show', $time->project->name, ['id' => $time->project->id]) !!}
                        <p class="small">↳
                            {!! link_to_route('client.show', $time->clientName, ['id' => $time->clientId]) !!}
                        </p>
                </td>
                @endunless

                <td>
                    {{ str_limit($time->summary, 75) }}

                    @if (count($time->tags) > 0)
                        <p>
                            @foreach ($time->tags as $tag)
                                <a href="{{ route('time.index', ['q' => "tag:{$tag->name}"]) }}">
                                    <span class="tag">
                                        <svg class="icon">
                                            <use xlink:href="#icon-price-tag" />
                                        </svg>
                                        {{ $tag->name }}
                                    </span>
                                </a>
                            @endforeach
                        </p>
                    @endif
                </td>

                @unless(Route::is('dashboard'))
                <td class="text-right">
                    @if ($time->accuracy)
                        {{ $time->accuracy }}%
                    @endif
                </td>
                @endunless

                @unless(Route::is('dashboard'))
                <td class="text-center">
                    @unless ($time->billable)
                        <svg class="icon inactive"><use xlink:href="#icon-blocked" /></svg>
                    @endunless

                    @if ($time->invoice_id)
                        <svg class="icon active"><use xlink:href="#icon-checkmark" /></svg>
                    @endif
                </td>
                @endunless
            </tr>
        @endforeach
    </tbody>
</table>
@endif
