<div class="form-group row {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'offset-sm-2' }}">
        {!! Form::textarea($name, isset($model)? $model->$name : '', ['class' => 'form-control']) !!}
        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif

        @if (isset($autofill))
            <autofill-hint
                field-selector="TEXTAREA[name={{ $name }}]"
                v-bind:suggestion="suggested{{ ucfirst($name) }}"
                v-bind:previous="previous{{ ucfirst($name) }}"
            />
        @endif
    </div>
</div>
