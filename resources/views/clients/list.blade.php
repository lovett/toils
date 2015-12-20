@extends('layouts.master')

@section('page_main')
    <ul>
    @foreach ($clients as $client)
        <li><a href="{{ route('client.edit', ['id' => $client->id]) }}">{{ $client->name }}</a></li>
    @endforeach
    </ul>


@endsection
