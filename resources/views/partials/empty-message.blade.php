@if ($collection->isEmpty())
    @isset($message)
        <p>{{ $message }}</p>
    @else
        <p>None</p>
    @endif
@endif
