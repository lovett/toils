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
                @php($upcomingClass = ($invoice->daysUntilDue >= 0 && $invoice->isPaid === false)? 'table-warning' : '')
                @php($abandonnedClass = ($invoice->abandonned)? 'table-dark' : '')
                <tr class="{{ $overdueClass }} {{ $upcomingClass }} {{ $abandonnedClass }}">
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
                            @include('partials.project-and-client', ['projectId' => $invoice->project_id, 'projectName' => $invoice->projectName, 'clientId' => $invoice->client_id, 'clientName' => $invoice->clientName])
                        </td>
                    @endunless
                    <td>
                        {{ TimeHelper::readableShortDate($timezone, $invoice->start) }} →
                        {{ TimeHelper::readableShortDate($timezone, $invoice->end) }}
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
                        @if ($invoice->isPaid)
                            @if ($invoice->receipt)
                            <a target="_blank" href="{{ route('invoice.receipt', $invoice->id) }}">
                                paid
                            </a>
                            @else
                            paid
                            @endif
                            <svg class="icon active after-text"><use xlink:href="#icon-coin-dollar"></use></svg>
                            <p class="small">
                                on {{ TimeHelper::date($timezone, $invoice->paid) }}
                            </p>
                        @elseif ($invoice->daysUntilDue >= 0)
                            waiting
                            <svg class="icon after-text"><use xlink:href="#icon-clock"></use></svg>
                            <p class="small">
                                due in
                                {{ $invoice->daysUntilDue }}
                                {{ str_plural('day', $invoice->daysUntilDue) }}
                            </p>
                        @elseif ($invoice->abandonned)
                            abandonned
                            <p class="small">
                                {{ TimeHelper::date($timezone, $invoice->abandonned) }}
                            </p>
                        @else
                            overdue
                            <svg class="icon inactive after-text"><use xlink:href="#icon-alarm"></use></svg>
                            <p class="small">
                                by
                                {{ abs($invoice->daysUntilDue) }}
                                {{ str_plural('day', $invoice->daysUntilDue) }}
                            </p>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
