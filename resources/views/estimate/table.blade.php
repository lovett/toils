<table class="table mb-0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Fee</th>
            <th>History</th>
            <th>Recipient</th>

            @unless(Route::is('client.show'))
                <th>Client</th>
            @endunless
            <th>Hours</th>
            <th>Status</th>
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
                    {{ CurrencyHelper::money($estimate->fee) }}
                </td>
                <td>
                    @if ($estimate->updated_at > $estimate->created_at)
                        <p class="mb-0">updated {{ TimeHelper::date($estimate->updated_at) }} at {{ TimeHelper::time($estimate->updated_at) }}</p>
                    @endif

                    @if ($estimate->submitted)
                        <p class="mb-0">submitted {{ TimeHelper::date($estimate->submitted) }}</p>
                    @endif

                    <p class="mb-0">created {{ TimeHelper::date($estimate->created_at) }} at {{ TimeHelper::time($estimate->created_at) }}</p>
                </td>
                <td>
                    {{ $estimate->recipient ?? 'None' }}
                </td>
                @unless(Route::is('client.show'))
                <td>
                    @if ($estimate->clientId)
                    <a href="{{ route('client.show', ['record' => $estimate->clientId]) }}">
                        {{ $estimate->clientName }}
                    </a>
                    @else
                    None
                    @endif
                </td>
                @endunless
                <td>
                    {{ $estimate->hours ?? '—' }}
                </td>
                <td>
                    {{ $estimate->status }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
