<div class="container">
    {!! Form::open(['route' => $route,  'method' => 'get']) !!}

    <facet-search facets="{{ implode(', ', $fields) }}" query="{{ $query }}"></facet-search>

    {!!  Form::close() !!}
</div>
