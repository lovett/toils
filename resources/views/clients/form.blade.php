@extends('layouts.master')

@section('page_main')

    @include('partials.error-alert')

{!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

@include('partials.formgroup-checkbox', ['name' => 'active', 'label' => 'This client is active', 'checked' => $model->active])

@include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

@include('partials.formgroup-standard', ['name' => 'contact_name', 'label' => 'Contact Name'])

@include('partials.formgroup-standard', ['name' => 'contact_email', 'label' => 'Contact Email'])

@include('partials.formgroup-standard', ['name' => 'address1', 'label' => 'Mailing Address'])

@include('partials.formgroup-standard', ['name' => 'address2', 'label' => null])

@include('partials.formgroup-standard', ['name' => 'city', 'label' => 'City'])

@include('partials.formgroup-standard', ['name' => 'locality', 'label' => 'State'])

@include('partials.formgroup-standard', ['name' => 'postal_code', 'label' => 'Zip'])

@include('partials.formgroup-standard', ['name' => 'phone', 'label' => 'Phone'])

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
{!! Form::model($model, ['route' => ['client.destroy', $model->id], 'method' => 'DELETE']) !!}
{!! Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger']) !!}
{!! Form::close() !!}
@endif
@endsection
