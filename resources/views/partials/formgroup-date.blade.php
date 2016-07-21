<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        <pickable inline-template format="yyyy-mm-dd" initial="{{ date('Y-m-d') }}">
            {!! Form::text($name, isset($model->$name)? $model->$name->format('Y-m-d') : '', ['class' => 'form-control', 'v-model' => 'pickResult']) !!}

            <div class="shortcuts">
                <p>
                    @foreach ($ranges['month'] as $value)
                        <a @click.prevent="pick('mm', '{{ $value->format('m') }}', 'active', $event)" href="#">{{ $value->format('M') }}</a>
                    @endforeach
                </p>
                <p>
                    @foreach ($ranges['day'] as $value)
                        <a @click.prevent="pick('dd', '{{ $value->format('d') }}', 'active', $event)" href="#">{{ $value->format('j') }}</a>
                    @endforeach
                </p>
                <p>
                    @foreach ($ranges['year'] as $value)
                        <a @click.prevent="pick('yyyy', '{{ $value->format('Y') }}', 'active', $event)" href="#">{{ $value->format('Y') }}</a>
                    @endforeach
                </p>
            </div>
        </pickable>

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
