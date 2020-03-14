@if (empty($vchange))
    @php($vchange='')
@endif

@if (!isset($readonly))
    @php($readonly=false)
@endif

@php($fieldClasses = ['form-control'])
@if ($errors->has($name))
    @php($fieldClasses[] = 'is-invalid')
@endif

@php($fieldContainerClasses = ['col-sm-10'])
@if (empty($label))
    @php($fieldContainerClasses[] = 'offset-sm-2')
@endif

@if ($readonly)
    @php($fieldContainerClasses[] = 'd-flex align-items-center')
@endif

<div class="form-group row">
    @if (!empty($label))
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="{{ implode(' ', $fieldContainerClasses) }}">
        @if ($readonly)
            {!! Form::hidden($name, $selectedItem) !!}
           {{ $items[$selectedItem] }}
        @endif

        @unless ($readonly)
            {!! Form::select($name, $items, old($name, $selectedItem), ['class' => $fieldClasses, 'ref' => 'autofillTrigger', 'v-on:change' => $vchange]) !!}
        @endunless

        @include('partials.form-field-error')
    </div>
</div>
