@extends('layouts.master')

@section('page_main')
    <ul>
	@foreach ($models as $model)
	<li><a href="{{ route('client.edit', ['id' => $model->id]) }}">{{ $model->name }}</a></li>
	@endforeach
    </ul>
@endsection

@section('nav_primary')
    {!! link_to_route('client.create', 'Add a client') !!}
@endsection
