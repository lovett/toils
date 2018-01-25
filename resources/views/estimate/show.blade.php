@extends('layouts.app')

@section('page_main')
    <div class="container">
        <h1>{{ $model->name }}</h1>
    </div>

    @include('partials.timestamps-footer', ['record' => $model])
@endsection

@section('subnav_supplemental')
@endsection
