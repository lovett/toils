@php($fieldClasses = ['form-control'])
@if ($errors->has($name))
    @php($fieldClasses[] = 'is-invalid')
@endif

<div class="form-group row">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'offset-sm-2' }}">
        @isset($pickable)
        <pickable
            name="{{ $name }}"
            groups="{{ implode($pickable, ',') }}"
            initial-value="{{ old($name, TimeHelper::date($model->$name)) }}"
            @isset($autofill)
            v-bind:suggested-value="suggested{{ ucfirst($name) }}"
            v-bind:previous-value="previous{{ ucfirst($name) }}"
            @endisset
            @if ($errors->has($name))
            error="{{ $errors->first($name) }}"
            @endif
        />
        @else
        {!! Form::text($name, old($name, TimeHelper::date($model->$name)), ['class' => $fieldClasses]) !!}

        @isset($autofill)
        <autofill-hint
            field-selector="INPUT[name={{ $name }}]"
            v-bind:suggestion="suggested{{ ucfirst($name) }}"
            v-bind:previous="previous{{ ucfirst($name) }}"
        />
        @endisset

        @include('partials.form-field-error')

        @endisset
    </div>
</div>
