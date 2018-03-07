<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        @if (isset($autofill))
            <autofill-hint
                field-selector="INPUT[name={{ $name }}]"
                v-bind:suggestion="suggested{{ ucfirst($name) }}"
                v-bind:previous="previous{{ ucfirst($name) }}">
                &nbsp;
            </autofill-hint>
        @endif

        @isset($pickable)
        <pickable
            name="{{ $name }}"
            groups="{{ implode($pickable, ',') }}"
            initial-value="{{ TimeHelper::date($model->$name) }}"
        />
        @else
        {!! Form::text($name, TimeHelper::date($model->$name), ['class' => 'form-control']) !!}
        @endisset

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
