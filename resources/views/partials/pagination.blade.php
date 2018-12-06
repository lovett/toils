<nav class="container text-center py-3">
    {{ $collection->appends(['q' => $query])->links() }}
</nav>
