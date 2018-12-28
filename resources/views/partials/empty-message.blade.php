@if ($collection->isEmpty())
    <p>{{ $message ?: "None." }}</p>
@endif
