<table class="table mb-0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Submitted</th>
            <th>Recipient</th>

            @unless(Route::is('client.show'))
                <th>Client</th>
            @endunless
            <th class="text-right">Hours</th>
            <th class="text-right">Fee</th>
            <th class="text-right">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $estimate)
            <tr>
                <td>
                    <a href="{{ route('estimate.edit', $estimate) }}">
                        {{ $estimate->name }}
                    </a>
                    <a href="{{ route('estimate.show', $estimate) }}" target="_blank">
                        <svg class="icon file-icon"><use xlink:href="#icon-file-pdf"></use></svg>
                    </a>
                </td>
                <td>
                    {{ TimeHelper::date($estimate->submitted) ?? '—' }}
                </td>
                <td>
                    {{ $estimate->recipient ?? '—' }}
                </td>
                @unless(Route::is('client.show'))
                <td>
                    @if ($estimate->clientId)
                        <a href="{{ route('client.show', $estimate->clientId) }}">
                            {{ $estimate->clientName }}
                        </a>
                    @else
                        —
                    @endif
                </td>
                @endunless
                <td class="text-right">
                    {{ $estimate->hours ?? '—' }}
                </td>
                <td class="text-right">
                    {{ CurrencyHelper::dollars($estimate->fee) }}
                </td>
                <td class="text-right">
                    {{ $estimate->status }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
