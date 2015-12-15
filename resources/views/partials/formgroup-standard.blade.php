<div class="form-group">
    @if ($label)
    {!! Form::label($name, $label, ['class' => 'col-sm-2 control-label']) !!}
    @endif

    <div class="col-sm-10 {{ $label ? '' : 'col-sm-offset-2' }}">
	{!! Form::text($name, null, ['class' => 'form-control']) !!}
    </div>
</div>
