<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
    {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
	{!! Form::text($name, isset($model->$name)? $model->$name->format('g:i A') : '', ['class' => 'form-control']) !!}

        <div class="shortcuts">
            <p>
                @foreach ($ranges['hour'] as $value)
                <a href="#{{ $value }}" class="hour">{{ $value }}</a>
                @endforeach
            </p>
            <p>
                @foreach ($ranges['minute'] as $value)
                <a href="#{{ $value }}" class="minute">{{ $value }}</a>
                @endforeach
            </p>
            <p>
                <a href="#AM" class="meridiem">AM</a>
                <a href="#PM" class="meridiem">PM</a>
            </p>
        </div>


        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
