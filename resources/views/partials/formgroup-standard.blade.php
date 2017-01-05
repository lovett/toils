<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
        {!! Form::text($name, isset($model)? $model->$name : '', ['class' => 'form-control']) !!}

        @if (isset($autofill))
            <autofill-hint target="INPUT[name={{ $name }}]" v-bind:value="suggested{{ ucfirst($name) }}" v-bind:previous="previous{{ ucfirst($name) }}"></autofill-hint>
        @endif

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
