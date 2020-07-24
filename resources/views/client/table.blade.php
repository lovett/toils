<table class="table mb-0">
    <thead>
        <tr>
            <th>Client</th>
            <th>Projects</th>
            <th>Total Time</th>
            <th>Last Active</th>
            <th>Created</th>
            <th class="text-right">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $client)
            <tr>
                <td>
                    {{ link_to_route('client.show', $client->name, ['client' => $client->id]) }}
                </td>
                <td>
                    {{ $client->projectCount }}
                </td>
                <td>
                    {{ TimeHelper::hoursAndMinutes($client->totalTime) }}
                </td>
                <td>
                    {{ TimeHelper::readableShortDate($timezone, $client->latestTime, 'never')}}
                </td>
                <td>
                    {{ TimeHelper::readableShortDate($timezone, $client->created_at) }}
                </td>
                <td class="text-right">
                    @include('partials.active', ['value' => $client->active])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
