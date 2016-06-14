@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'client.index', 'q' => $q])
    <div class="list collection">
        @foreach ($clients as $client)
            @include('partials.client', ['client' => $client])
        @endforeach
    </div>
@endsection

@section('nav_primary')
    {!! link_to_route('client.create', 'Add a client') !!}
@endsection
