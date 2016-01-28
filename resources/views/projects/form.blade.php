@extends('layouts.master')

@section('page_main')

@include('partials.error-alert')

{!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

@include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name', 'value' => $model->name])

@include('partials.formgroup-menu', ['name' => 'client_id', 'label' => 'Client', 'items' => $clients, 'selectedItem' => $model->client_id])

@include('partials.formgroup-checkbox', ['name' => 'active', 'label' => 'This project is active', 'checked' => $model->active])

@include('partials.formgroup-checkbox', ['name' => 'billable', 'label' => 'This project is billable', 'checked' => $model->billable])

@include('partials.formgroup-checkbox', ['name' => 'tax_deducted', 'label' => 'Tax will be deducted', 'checked' => $model->tax_deducted])


<div class="form-group">
    <div class="col-sm-12 text-center">
	{!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-default']) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection

@section('nav_primary')
{!! link_to_route('client.show', 'Cancel', ['client' => $model->client_id]) !!}
@endsection


@section('nav_supplemental')
@if ($model->id)
{!! Form::model($model, ['route' => ['project.destroy', $model->id], 'method' => 'DELETE']) !!}
{!! Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger']) !!}
{!! Form::close() !!}
@endif
@endsection
