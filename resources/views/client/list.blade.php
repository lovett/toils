@extends('layouts.app')

@section('page_main')

    @include('partials.search', ['route' => 'client.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('client.table', ['collection' => $clients])
    </div>
    @include('partials.pagination', ['collection' => $clients])
@endsection
