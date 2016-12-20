@extends('layouts.master')

@section('page_main')

@include('partials.error-alert')

{!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

@if (isset($previousModel))
<div class="well well-sm text-center">
    <a
        onclick="prefill(event)"
        href="#"
        data-project_id="{{ $previousModel->project_id }}"
	data-estimate="{{ $previousModel->estimatedDuration }}"
	data-summary="{{ $previousModel->summary}}"
    >Prefill from previous entry</a>
</div>
@endif

@include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id])


@include('partials.formgroup-date', ['name' => 'start', 'suffix' => 'Date', 'label' => 'Start', 'ranges' => TimeHelper::ranges()])

@include('partials.formgroup-date', ['name' => 'end', 'suffix' => 'Date', 'label' => 'End', 'ranges' => TimeHelper::ranges()])


@include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

@include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary'])

<div class="form-group">
    {!! Form::label('amount', 'Amount', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
        <div class="input-group">
            <div class="input-group-addon">$</div>
            {!! Form::text('amount', $model->amount, ['class' => 'form-control']) !!}
        </div>
    </div>

    {!! Form::label('receipt', 'Receipt', ['class' => 'col-sm-1 control-label']) !!}
    <div class="col-sm-2">
        <input type="file" id="receipt" />
    </div>
</div>

@include('partials.formgroup-date', ['name' => 'sent', 'suffix' => 'Date', 'label' => 'Sent', 'ranges' => TimeHelper::ranges()])

@include('partials.formgroup-date', ['name' => 'due', 'suffix' => 'Date', 'label' => 'Due', 'ranges' => TimeHelper::ranges()])


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
    @include('partials.vue')
    <script src="{{ asset('js/pickable.js') }}"></script>
    @include('partials.select2')
@endsection
