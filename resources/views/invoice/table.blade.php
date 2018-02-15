@if ($collection->isNotEmpty())
    <table class="table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Client</th>
                <th>Project</th>
                <th>Start</th>
                <th>End</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($collection as $invoice)
                @php($overdueClass = ($invoice->daysUntilDue < 0)? 'danger' : '')
                @php($upcomingClass = ($invoice->daysUntilDue > 0)? 'warning' : '')
                <tr class="{{ $overdueClass }} {{ $upcomingClass }}">
                    <td>
                        <a href="{{ route('invoice.show', $invoice) }}" target="_blank">
                            {{ $invoice->number }}
                            <svg class="icon file-icon"><use xlink:href="#icon-file-pdf"></use></svg>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('invoice.edit', $invoice) }}">
                            {{ $invoice->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('client.show', ['record' => $invoice->clientId]) }}">
                            {{ $invoice->clientName }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('project.show', ['record' => $invoice->projectId]) }}">
                            {{ $invoice->projectName }}
                        </a>
                    </td>
                    <td>
                        {{ TimeHelper::date($invoice->start) }}
                        <p class="small">
                            @if ($invoice->totalMinutes > 0)
                                <a href="{{ route('time.index', ['q' => "invoice:{$invoice->id}"]) }}">{{ TimeHelper::hoursAndMinutes($invoice->totalMinutes) }}</a>
                            @else
                                0 hours
                            @endif
                        </p>
                    </td>
                    <td>
                        {{ TimeHelper::date($invoice->end) }}
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
