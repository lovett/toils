<nav class="container text-center">
    {{ $collection->appends(['q' => $query])->links() }}
</nav>
