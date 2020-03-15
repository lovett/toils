<div class="form-group" @if (isset($hideable))v-show="hideables.indexOf('{{ $name }}') === -1" @endif>
    <div class="offset-sm-2 col-sm-10">
        <div class="checkbox">
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

                {{ $label }}
            </label>
        </div>
    </div>
</div>
