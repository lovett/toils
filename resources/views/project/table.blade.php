<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Unbilled Time</th>
            <th>Client</th>
            <th>Created</th>
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
                    @include('partials.active', ['value' => $project->active])
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
                <td>
                    {{ TimeHelper::readableShortDate($project->created_at) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
