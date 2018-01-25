@extends('layouts.app')

@section('page_main')
    @include('partials.search', ['route' => 'time.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('time.table', ['collection' => $times])
    </div>

    @include('partials.pagination', ['collection' => $times])
@endsection
