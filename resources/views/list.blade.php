@extends('layouts.app')

@section('subnav')
    <div class="container mb-4">
        <ul class="nav nav-tabs flex-column flex-lg-row">
            <li class="nav-item">
                {!! link_to_route("{$module}.create", 'New ' . ucfirst($module), [], ['class' => 'nav-link']) !!}
            </li>
        </ul>
    </div>
@endsection


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
