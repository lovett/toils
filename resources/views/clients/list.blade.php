@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'client.index', 'q' => $q])
    <div class="list collection">
        @foreach ($clients as $client)
        <div class="item">
            <h2 class="title">
                <a href="{{ route('client.show', ['client' => $client]) }}">{{ $client->name }}</a>
            </h2>
        </div>
        @endforeach
    </div>
@endsection

@section('nav_primary')
    {!! link_to_route('client.create', 'Add a client') !!}
@endsection
