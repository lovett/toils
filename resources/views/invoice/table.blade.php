@if ($collection->isNotEmpty())
    <table class="table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Client</th>
                <th>Project</th>
                <th>Time</th>
                <th class="text-right">Amount</th>
                <th class="text-right">Payment</th>
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
                        {{ TimeHelper::dateFromRaw($invoice->start) }} - {{ TimeHelper::dateFromRaw($invoice->end) }}
                        <p class="small">
                            {{ TimeHelper::hoursAndMinutes($invoice->totalMinutes) }}
                        </p>
                    </td>
                    <td class="text-right">
                        {{ CurrencyHelper::withSymbol($invoice->amount) }}
                    </td>
                    <td class="text-right">
                        @if($invoice->isPaid && $invoice->receipt)
                            <svg class="icon"><use xlink:href="#icon-file-empty"></use></svg>
                            <a href="{{ route('invoice.receipt', $invoice->id) }}">
                                {{ TimeHelper::date($invoice->paid) }}
                            </a>
                        @elseif ($invoice->isPaid)
                            {{ TimeHelper::date($invoice->paid) }}
                        @elseif ($invoice->daysUntilDue === 0)
                            <svg class="icon"><use xlink:href="#icon-clock"></use></svg>
                            due today
                        @elseif ($invoice->daysUntilDue > 0)
                            <svg class="icon"><use xlink:href="#icon-clock"></use></svg>
                            due in
                            {{ $invoice->daysUntilDue }}
                            {{ str_plural('day', $invoice->daysUntilDue) }}
                        @else
                            <svg class="icon inactive"><use xlink:href="#icon-blocked"></use></svg>
                            due
                            {{ abs($invoice->daysUntilDue) }}
                            {{ str_plural('day', $invoice->daysUntilDue) }} ago
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
