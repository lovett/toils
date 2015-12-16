@extends('layouts.master')

@section('page_main')
    <ul>
    @foreach ($clients as $client)
        <li>{{ $client->name }}</li>
    @endforeach
    </ul>


@endsection
