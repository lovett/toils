<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        @isset($pickable)
        <pickable
            name="{{ $name }}"
            groups="{{ implode($pickable, ',') }}"
            initial-value="{{ TimeHelper::date($model->$name) }}"
            @isset($autofill)
            v-bind:suggested-value="suggested{{ ucfirst($name) }}"
            v-bind:previous-value="previous{{ ucfirst($name) }}"
            @endisset
            @if ($errors->has($name))
            error="{{ $errors->first($name) }}"
            @endif
        />
        @else
        {!! Form::text($name, TimeHelper::date($model->$name), ['class' => 'form-control']) !!}
        @isset($autofill)
        <autofill-hint
            field-selector="INPUT[name={{ $name }}]"
            v-bind:suggestion="suggested{{ ucfirst($name) }}"
            v-bind:previous="previous{{ ucfirst($name) }}"
        />
        @endisset
        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif

        @endisset

    </div>
</div>
