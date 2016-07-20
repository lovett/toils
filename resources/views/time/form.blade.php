@extends('layouts.master')

@section('page_main')

@include('partials.error-alert')

{!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

@include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary'])

@include('partials.formgroup-date', ['name' => 'start', 'label' => 'Date', 'ranges' => $ranges])

@include('partials.formgroup-time', ['name' => 'start', 'label' => 'Start', 'ranges' => $ranges])
@include('partials.formgroup-time', ['name' => 'end', 'label' => 'End', 'ranges' => $ranges])

@include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id])

@include('partials.formgroup-standard', ['name' => 'estimatedDuration', 'label' => 'Estimate'])

<div class="form-group">
    <div class="col-sm-12 text-center">
	{!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-default']) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('nav_primary')
{!! link_to($backUrl, 'Cancel') !!}
@endsection

@section('nav_supplemental')

@if ($model->id)
{!! Form::model($model, ['route' => ['time.destroy', $model->id], 'method' => 'DELETE']) !!}
{!! Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger']) !!}
{!! Form::close() !!}
@endif

@endsection

@section('page_scripts')
<script src="{{ asset('js/vue.min.js') }}"></script>
<script src="{{ asset('js/time.js') }}"></script>
@endsection
