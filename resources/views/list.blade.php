@extends('layouts.app')

@section('page_main')

    @include('partials.search', ['route' => $module . '.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('partials.empty-message', ['collection' => $collection, 'message' => $emptyMessage])

        @if ($collection->isNotEmpty())
            <div class="card">
                @include($module . '.table', ['collection' => $collection])
            </div>
        @endif
    </div>

    @include('partials.pagination', ['collection' => $collection])

@endsection
