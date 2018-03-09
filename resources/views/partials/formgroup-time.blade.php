<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        @isset($pickable)
        <pickable
            name="{{ $fieldName }}"
            format="h:mm A"
            groups="{{ implode($pickable, ',') }}"
            initial-value="{{ TimeHelper::date($model->$name) }}"
            @isset($autofill)
            v-bind:suggested-value="suggested{{ ucfirst($fieldName) }}"
            v-bind:previous-value="previous{{ ucfirst($fieldName) }}"
            @endif
        />
        @else
        {!! Form::text($fieldName, TimeHelper::time($model->$name), ['class' => 'form-control']) !!}
        @if (isset($autofill))
            <autofill-hint
                field-selector="INPUT[name={{ $model->$name }}]"
                v-bind:suggestion="suggested{{ ucfirst($model->$name) }}"
                v-bind:previous="previous{{ ucfirst($model->$name) }}"
            />
        @endif
        @endisset

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
