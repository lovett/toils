<div class="offset-sm-2">
    <p class="alert alert-info" role="alert">
        <svg class="icon"><use xlink:href="#icon-bullhorn"></use></svg>
        Only showing projects for {{ $client->name }}.
        {{ link_to_route(Route::currentRouteName(), 'Show all projects instead') }}.
    </p>
</div>
