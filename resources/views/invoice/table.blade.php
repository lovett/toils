@if ($collection->isNotEmpty())
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Name</th>
                @unless(Route::is('project.show'))
                    <th>Project</th>
                @endunless
                <th width="275" >Dates</th>
                <th class="text-right">Amount</th>
                <th width="150"  class="text-right">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($collection as $invoice)
                @php($overdueClass = ($invoice->daysUntilDue < 0)? 'table-danger' : '')
                @php($upcomingClass = ($invoice->daysUntilDue > 0)? 'table-warning' : '')
                <tr class="{{ $overdueClass }} {{ $upcomingClass }}">
                    <td>
                        <a href="{{ route('invoice.edit', $invoice) }}">
                            {{ $invoice->name }}
                        </a>
                        <p class="small">#{{ $invoice->number }}</p>
                        <a href="{{ route('invoice.show', $invoice) }}" target="_blank">
                            <svg class="icon file-icon"><use xlink:href="#icon-file-pdf"></use></svg>
                        </a>
                    </td>
                    @unless(Route::is('project.show'))
                    <td>
                        <div>
                            <a href="{{ route('project.show', ['record' => $invoice->projectId]) }}">
                                {{ $invoice->projectName }}
                            </a>

                            @if ($invoice->clientName)
                            <p class="small">↳ <a href="{{ route('client.show', ['record' => $invoice->clientId]) }}">
                                {{ $invoice->clientName }}
                            </p>
                            @endif
                        </div>
                    </td>
                    @endunless
                    <td>
                        {{ TimeHelper::readableShortDate($invoice->start) }} →
                        {{ TimeHelper::readableShortDate($invoice->end) }}
                        <p class="small">
                            @if ($invoice->totalMinutes > 0)
                                <a href="{{ route('time.index', ['q' => "invoice:{$invoice->number}"]) }}">{{ TimeHelper::hoursAndMinutes($invoice->totalMinutes) }}</a>
                            @else
                                0 hours
                            @endif
                        </p>
                    </td>
                    <td class="text-right">
                        {{ CurrencyHelper::money($invoice->amount) }}
                    </td>
                    <td class="text-right">
                        @if ($invoice->isPaid && $invoice->receipt)
                            <svg class="icon"><use xlink:href="#icon-file-empty"></use></svg>
                            <a href="{{ route('invoice.receipt', $invoice->id) }}">
                                paid
                            </a>
                            <p class="small">
                                on {{ TimeHelper::date($invoice->paid) }}
                            </p>
                        @elseif ($invoice->isPaid)
                            paid
                            <p class="small">
                                {{ TimeHelper::date($invoice->paid) }}
                            </p>
                        @elseif ($invoice->daysUntilDue > 0)
                            waiting
                            <p class="small">
                                {{ $invoice->daysUntilDue }}
                                {{ str_plural('day', $invoice->daysUntilDue) }}
                                <svg class="icon"><use xlink:href="#icon-clock"></use></svg>
                            </p>
                        @else
                            overdue
                            <p class="small">
                                {{ abs($invoice->daysUntilDue) }}
                                {{ str_plural('day', $invoice->daysUntilDue) }}
                                <svg class="icon inactive"><use xlink:href="#icon-blocked"></use></svg>
                            </p>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
