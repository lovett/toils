<table class="table mb-0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Unbilled Time</th>
            <th>Client</th>
            <th class="text-right">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $project)
            <tr>
                <td>
                    <a href="{{ route('project.show', ['record' => $project]) }}">
                        {{ $project->name }}
                    </a>
                </td>
                <td>
                    {{ TimeHelper::hoursAndMinutes($project->unbilledTime) }}
                </td>
                <td>
                    @if ($project->client)
                        {!! link_to_route('client.show', $project->client->name, ['id' => $project->client->id]) !!}
                    @else
                        none
                    @endif
                </td>
                <td class="text-right">
                    @include('partials.active', ['value' => $project->active])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
