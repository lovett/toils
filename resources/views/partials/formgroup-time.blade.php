<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        <pickable inline-template format="hh:mm AA" initial="{{ date('h:i A') }}">

	    {!! Form::text($name, isset($model->$name)? $model->$name->format('g:i A') : '', ['class' => 'form-control', 'v-model' => 'pickResult']) !!}

            <div class="shortcuts">
                <p>
                    @foreach ($ranges['hour'] as $value)
                        <a @click.prevent="pick('hh', '{{ $value->format('g') }}', 'active', $event)" href="#">{{ $value->format('g') }}</a>
                    @endforeach
                </p>
                <p>
                    @foreach ($ranges['minute'] as $value)
                        <a @click.prevent="pick('mm', '{{ $value }}', 'active', $event)" href="#">{{ $value }}</a>
                    @endforeach
                </p>
                <p>
                    <a @click.prevent="pick('AA', 'AM', 'active', $event)" href="#">AM</a>
                    <a @click.prevent="pick('AA', 'PM', 'active', $event)" href="#">PM</a>
                </p>
            </div>
        </pickable>


        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
