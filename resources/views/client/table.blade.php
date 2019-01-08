<table class="table mb-0">
    <thead>
        <tr>
            <th>Client</th>
            <th>Status</th>
            <th>Projects</th>
            <th>Total Time</th>
            <th>Last Active</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $client)
            <tr>
                <td>
                    <a href="{{ route('client.show', ['record' => $client]) }}">
                        {{ $client->name }}
                    </a>
                </td>
                <td>
                    @include('partials.active', ['value' => $client->active])
                </td>
                <td>
                    {{ $client->projectCount }}
                </td>
                <td>
                    {{ TimeHelper::hoursAndMinutes($client->totalTime) }}
                </td>
                <td>
                    {{ TimeHelper::readableShortDate($client->latestTime, 'never')}}
                </td>
                <td>
                    {{ TimeHelper::readableShortDate($client->created_at) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
