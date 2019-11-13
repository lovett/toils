@extends('layouts.app')

@section('page_main')

    @include('partials.search', ['route' => $module . '.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @if ($collection->isEmpty())
            <p>No records found.</p>
        @endif

        @unless ($collection->isEmpty())
            <div class="card">
                @include($module . '.table', ['collection' => $collection])
            </div>
        @endunless
    </div>

    <nav aria-label="Pagination" class="d-flex justify-content-center py-4">
        {{ $collection->appends(['q' => $query])->links() }}
    </nav>

@endsection
