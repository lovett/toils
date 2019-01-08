@php($fieldClasses = ['form-control'])
@if ($errors->has($name))
    @php($fieldClasses[] = 'is-invalid')
@endif

<div class="form-group row">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'offset-sm-2' }}">
        {!! Form::textarea($name, isset($model)? old($name, $model->$name) : '', ['class' => $fieldClasses]) !!}

        @include('partials.form-field-error')

        @if (isset($autofill))
            <autofill-hint
                field-selector="TEXTAREA[name={{ $name }}]"
                v-bind:suggestion="suggested{{ ucfirst($name) }}"
                v-bind:previous="previous{{ ucfirst($name) }}"
            />
        @endif
    </div>
</div>
