@extends('layouts.app')

@section('page_main')

    @include('partials.search', ['route' => 'estimate.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('partials.empty-message', ['collection' => $estimates, 'message' => $emptyMessage])

        @if ($estimates->isNotEmpty())
            @include('estimate.table', ['collection' => $estimates])
        @endif
    </div>

    @include('partials.pagination', ['collection' => $estimates])

@endsection
