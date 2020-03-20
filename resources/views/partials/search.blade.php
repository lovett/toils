<div class="container">
    {!! Form::open(['route' => $route,  'method' => 'get']) !!}

    @if (sizeof($fields) > 1)
        <facet-search facets="{{ implode(', ', $fields) }}" query="{{ $query }}"></facet-search>
    @else
        <div class="plain-search">
            <div class="input-group">
                <input type="search" ref="q" name="q" class="form-control" placeholder="Search…" />
                <span class="input-group-append">
                    <button class="btn" type="submit">Search</button>
                </span>
            </div>
        </div>
    @endif

    {!!  Form::close() !!}
</div>
