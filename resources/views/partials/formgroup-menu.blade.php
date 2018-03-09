@if (empty($vchange))
    @php($vchange='')
@endif

<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if (!empty($label))
        {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    @php($colClasses = ['col-sm-10'])
    @if (empty($label))
        @php($colClasses[] = 'col-sm-offset-2')
    @endif

    <div class="{{ implode(' ', $colClasses) }}">
        {!! Form::select($name, $items, $selectedItem, ['class' => 'form-control', 'ref' => 'autofillTrigger', 'v-on:change' => $vchange]) !!}

        @if ($errors->has($name))
            <div class="help-block">{{ $errors->first($name)}}</div>
        @endif
    </div>
</div>
