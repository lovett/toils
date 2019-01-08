@if ($collection->isEmpty())

    @isset($query)
    <p>No {{ $collectionOf }} found for this search.</p>
    @else
    <p>There are no {{ $collectionOf }}.</p>
    @endif

@endif
