<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        {!! Form::text($fieldName, TimeHelper::timeField($model->$name), ['class' => 'form-control']) !!}

        <pickable
            format="hh:mm A"
            field-selector="INPUT[name={{ $fieldName }}]">
            &nbsp;
        </pickable>

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
