<table class="table mb-0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Last Time Entry</th>
            <th>Billed Time</th>
            <th>Unbilled Time</th>
            <th class="text-right">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collection as $project)
            <tr>
                <td>
                    @include('partials.project-and-client', ['projectId' => $project->id, 'projectName' => $project->name, 'clientId' => $project->client->id, 'clientName' => $project->client->name])
                </td>
                <td>
                    {{ TimeHelper::date($project->lastActive) }}
                </td>
                <td>
                    {{ TimeHelper::hoursAndMinutes($project->billedTime) }}
                </td>
                <td>
                    {{ TimeHelper::hoursAndMinutes($project->unbilledTime) }}
                </td>
                <td class="text-right">
                    @include('partials.active', ['value' => $project->active])
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
