<div class="form-group row" @if (isset($hideable))v-show="hideables.indexOf('{{ $name }}') === -1" @endif>

    @if ($label)
    <div class="col-sm-2 text-right">
        {{ $label }}
    </div>
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'offset-sm-2' }}">
        <label>
            {!! Form::hidden($name, 0) !!}
            @if (isset($settable))
                <input
                    type="checkbox"
                    name="{{ $name }}"
                    value="1"
                    v-model="{{ $name }}"
                />
            @else
                {!! Form::checkbox($name, 1, ) !!}
            @endif

            {{ $field_label }}
        </label>
    </div>
</div>
