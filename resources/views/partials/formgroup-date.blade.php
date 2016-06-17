<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
    {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
	{!! Form::text($name, isset($record->$name)? $record->$name->format('Y-m-d') : '', ['class' => 'form-control']) !!}

        <div class="shortcuts">
            <p>
                @foreach ($ranges['month'] as $value)
                <a href="#{{ $value->format('M') }}" class="month">{{ $value->format('M') }}</a>
                @endforeach
            </p>
            <p>
                @foreach ($ranges['day'] as $value)
                <a href="#{{ $value->format('d') }}" class="day">{{ $value->format('j') }}</a>
                @endforeach
            </p>
            <p>
                @foreach ($ranges['year'] as $value)
                <a href="#{{ $value->format('Y') }}" class="year">{{ $value->format('Y') }}</a>
                @endforeach
            </p>
        </div>


        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
