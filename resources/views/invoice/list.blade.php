@extends('layouts.app')

@section('page_main')

    @include('partials.search', ['route' => 'invoice.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('partials.empty-message', ['collection' => $invoices])

        @include('invoice.table', ['collection' => $invoices])
    </div>

    @include('partials.pagination', ['collection' => $invoices])

@endsection
