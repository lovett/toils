{!! Form::open(['route' => $searchRoute,  'method' => 'get', 'class' => 'search']) !!}

<div class="form-group">
    <div class="input-group">
	<input type="search" name="q" class="form-control" placeholder="Search" value="{{ $search }}">
	<span class="input-group-btn">
            <button class="btn btn-default" type="submit">Search</button>
	    @if ($search)
		<a href="{{ route($searchRoute) }}" class="btn" type="button">Reset</a>
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
