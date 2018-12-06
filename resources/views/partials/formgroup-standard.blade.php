

<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-{{ isset($size)? $size : 8 }} {{ $label ? '' : 'col-sm-offset-2' }}">
        <div class="input-group">
            {!! Form::text($name, isset($model)? $model->$name : '', ['class' => 'form-control']) !!}
            @if (isset($inlineSuffix))
                <div class="input-group-addon">{{ $inlineSuffix }}</div>
            @endif
            @if (isset($autofill))
                <autofill-hint
                    field-selector="INPUT[name={{ $name }}]"
                    v-bind:suggestion="suggested{{ ucfirst($name) }}"
                    v-bind:previous="previous{{ ucfirst($name) }}">
                    &nbsp;
                </autofill-hint>
            @endif
        </div>


        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>



</div>
