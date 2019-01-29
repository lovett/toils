@extends('layouts.app')

@section('page_main')

    @include('partials.search', ['route' => $module . '.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('partials.empty-message')

        @if ($collection->isNotEmpty())
            <div class="card">
                @include($module . '.table', ['collection' => $collection])
            </div>
        @endif
    </div>

    <nav aria-label="Pagination" class="d-flex justify-content-center py-4">
        {{ $collection->appends(['q' => $query])->links() }}
    </nav>

@endsection
