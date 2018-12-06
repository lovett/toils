<div class="form-group row {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="col-sm-{{ isset($size)? $size : 10 }} {{ $label ? '' : 'offset-sm-2' }}">
        <div class="input-group">
            {!! Form::text($name, isset($model)? $model->$name : '', ['class' => 'form-control']) !!}
            @if (isset($inlineSuffix))
                <div class="input-group-append">
                    <span class="input-group-text">{{ $inlineSuffix }}</span>
                </div>
            @endif
        </div>
        @if (isset($autofill))
            <autofill-hint
                field-selector="INPUT[name={{ $name }}]"
                v-bind:suggestion="suggested{{ ucfirst($name) }}"
                v-bind:previous="previous{{ ucfirst($name) }}">
                &nbsp;
            </autofill-hint>
        @endif


        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>



</div>
