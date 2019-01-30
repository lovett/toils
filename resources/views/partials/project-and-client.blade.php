<div>
    {!! link_to_route('project.show', $projectName, ['id' => $projectId]) !!}

    @if ($clientName)
        <p class="small">↳ {!! link_to_route('client.show', $clientName, ['id' => $clientId]) !!}</p>
    @endif
</div>
