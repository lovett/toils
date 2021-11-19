@php($fieldClasses = ['form-control'])
@if ($errors->has($name))
    @php($fieldClasses[] = 'is-invalid')
@endif

@if ($fieldName && $errors->has($fieldName))
    @php($fieldClasses[] = 'is-invalid')
    @php($pickableError = $errors->first($fieldName))
@else
    @php($pickableError = null)
@endif

<div class="form-group row">
    @if ($label)
        {!! Form::label($name, $label, ['class' => 'col-sm-2 col-form-label text-right']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'offset-sm-2' }}">
        @isset($pickable)
        <pickable
            name="{{ $fieldName }}"
            format="hh:mm"
            groups="{{ implode(',', $pickable) }}"
            initial-value="{{ old($fieldName, TimeHelper::time24($timezone, $model->$name)) }}"
            input-type="time"
            @isset($autofill)
            v-bind:suggested-value="suggested{{ ucfirst($fieldName) }}"
            v-bind:previous-value="previous{{ ucfirst($fieldName) }}"
            @endif
            error="{{ $pickableError }}"
        />
        @else
        {!! Form::text($fieldName, old($fieldName, TimeHelper::time24($timezone, $model->$name)), ['type' => 'time', 'class' => $fieldClasses]) !!}

        @isset($autofill)
        <autofill-hint
            field-selector="INPUT[name={{ $model->$name }}]"
            v-bind:suggestion="suggested{{ ucfirst($model->$name) }}"
            v-bind:previous="previous{{ ucfirst($model->$name) }}"
        />
        @endisset

        @include('partials.form-field-error')

        @endisset

    </div>
</div>
