@if ($collection->isEmpty() === 0)
    <p>{{ $message or "None" }}</p>
@endif
