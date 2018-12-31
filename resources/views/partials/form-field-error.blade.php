@if ($errors->has($name))
    <div class="invalid-feedback" role="alert">{{ $errors->first($name)}}</div>
@endif
