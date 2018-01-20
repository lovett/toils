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
            </tr>
        </thead>
        <tbody>
            @foreach ($collection as $invoice)
                <tr>
                    <td>
                        {{ $invoice->number }}
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
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
