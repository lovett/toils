@if (empty($vchange))
    @php($vchange='')
@endif

@php($fieldClasses = ['form-control'])
@if ($errors->has($name))
    @php($fieldClasses[] = 'is-invalid')
@endif

@php($fieldContainerClasses = ['col-sm-10'])
@if (empty($label))
    @php($fieldContainerClasses[] = 'offset-sm-2')
@endif

<div class="form-group row">
    @if (!empty($label))
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="{{ implode(' ', $fieldContainerClasses) }}">
        {!! Form::select($name, $items, $selectedItem, ['class' => $fieldClasses, 'ref' => 'autofillTrigger', 'v-on:change' => $vchange]) !!}

        @include('partials.form-field-error')
    </div>
</div>
