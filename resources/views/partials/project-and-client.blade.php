<div>
    {!! link_to_route('project.show', $projectName, ['project' => $projectId]) !!}

    @if ($clientName)
        <p class="small">↳ {!! link_to_route('client.show', $clientName, ['client' => $clientId]) !!}</p>
    @endif
</div>
