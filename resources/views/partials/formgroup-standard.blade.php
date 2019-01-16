@php($fieldClasses = ['form-control'])
@if ($errors->has($name))
    @php($fieldClasses[] = 'is-invalid')
@endif

<div class="form-group row">
    @if ($label)
        <div class="col-sm-2 text-right">
            {!! Form::label($name, $label, ['class' => 'col-form-label']) !!}
        </div>
    @endif

    <div class="col-sm-{{ isset($size)? $size : 10 }} {{ $label ? '' : 'offset-sm-2' }}">
        <div class="input-group">
            {!! Form::text($name, isset($model)? old($name, $model->$name) : '', ['class' => $fieldClasses]) !!}
            @if (isset($inlineSuffix))
                <div class="input-group-append">
                    <span class="input-group-text">{{ $inlineSuffix }}</span>
                </div>
            @endif

            @include('partials.form-field-error')
        </div>

        @include('partials.form-field-help')

        @if (isset($autofill))
            <autofill-hint
                field-selector="INPUT[name={{ $name }}]"
                v-bind:suggestion="suggested{{ ucfirst($name) }}"
                v-bind:previous="previous{{ ucfirst($name) }}">
                &nbsp;
            </autofill-hint>
        @endif

    </div>
</div>
