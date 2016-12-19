{!! Form::open(['route' => $route,  'method' => 'get', 'class' => 'search']) !!}

<div class="form-group">
    <searchby inline-template terms="{{ $search }}">
        <div class="input-group">
            <input type="search" name="q" class="form-control" placeholder="Search" v-el:field v-model="terms" value="{{ $search }}">
            <span class="input-group-btn">
                <button class="btn btn-default" type="submit">Search</button>
                @if ($search)
                    <a href="{{ route($route) }}" class="btn" type="button">Reset</a>
                @endif
            </span>
        </div>

        @if (!empty($fields))
            <ul class="small list-inline facets">
                @foreach ($fields as $field)
                    <li><a @click.prevent="applyField('{{ $field }}')" href="#">{{ ucfirst($field) }}</a></li>
                @endforeach
            </ul>
        @endif
    </searchby>
</div>

{!!  Form::close() !!}
