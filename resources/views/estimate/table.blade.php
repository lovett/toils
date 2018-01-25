<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Recipient</th>
            <th>Client</th>
            <th>Fee</th>
            <th>Hours</th>
            <th>Status</th>
            <th class="text-right">Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $estimate)
            <tr>
                <td>
                    <a href="{{ route('estimate.edit', ['record' => $estimate]) }}">
                        {{ $estimate->name }}
                    </a>
                </td>
                <td>
                    {{ $estimate->recipient }}
                </td>
                <td>
                    @if ($estimate->clientId)
                    <a href="{{ route('client.show', ['record' => $estimate->clientId]) }}">
                        {{ $estimate->clientName }}
                    </a>
                    @else
                    none
                    @endif
                </td>
                <td>
                    {{ CurrencyHelper::wholeNumberWithSymbol($estimate->fee) }}
                </td>
                <td>
                    {{ $estimate->hours }}
                </td>
                <td>
                    {{ $estimate->status }}
                </td>

                <td class="text-right">
                    @if($estimate->closed)
                        closed {{ TimeHelper::date($estimate->closed) }}
                    @elseif($estimate->submitted)
                        submitted {{ TimeHelper::date($estimate->submitted) }}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
