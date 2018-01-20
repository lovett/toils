@extends('layouts.app')

@section('page_main')
    @include('partials.search', ['route' => 'project.index', 'query' => $query, 'fields' => $searchFields])

    <div class="container">
        @include('project.table', ['collection' => $projects])
    </div>

    @include('partials.pagination', ['collection' => $projects])
@endsection
