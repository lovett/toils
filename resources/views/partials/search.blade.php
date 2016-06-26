{!! Form::open(['route' => $searchRoute,  'method' => 'get', 'class' => 'search']) !!}

<div class="form-group">
    <div class="input-group">
	<input type="search" name="q" class="form-control" placeholder="Search" value="{{ $q }}">
	<span class="input-group-btn">
            <button class="btn btn-default" type="submit">Search</button>
	    @if ($q)
		<a href="{{ route($search_route) }}" class="btn" type="button">Reset</a>
	    @endif
	</span>
    </div>

    @if (!empty($fields))
    <ul class="small list-inline facets">
	@foreach ($fields as $field)
    	<li><a href="#{{ $field }}">{{ ucfirst($field) }}</a></li>
	@endforeach
    </ul>
    @endif

</div>

{!!  Form::close() !!}
