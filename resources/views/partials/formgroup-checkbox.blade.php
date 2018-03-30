<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
            <label>
                {!! Form::hidden($name, 0) !!}
                {!! Form::checkbox($name, 1, $checked) !!}
                {{ $label }}
            </label>
        </div>
    </div>
</div>
