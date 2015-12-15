@extends('layouts.master')

@section('page_main')

{!! Form::model($client, ['route' => 'clients::store', 'class' => 'form-horizontal']) !!}

<p class="text-right">
    <a href="#">Cancel</a>
</p>

@include('partials.formgroup-checkbox', ['name' => 'active', 'label' => 'This client is active', 'checked' => true])

@include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

@include('partials.formgroup-standard', ['name' => 'contact_name', 'label' => 'Contact Name'])

@include('partials.formgroup-standard', ['name' => 'contact_email', 'label' => 'Contact Email'])

@include('partials.formgroup-standard', ['name' => 'address1', 'label' => 'Mailing Address'])

@include('partials.formgroup-standard', ['name' => 'address2', 'label' => null])

@include('partials.formgroup-standard', ['name' => 'city', 'label' => 'City'])

@include('partials.formgroup-standard', ['name' => 'state', 'label' => 'State'])

@include('partials.formgroup-standard', ['name' => 'postal_code', 'label' => 'Zip'])

@include('partials.formgroup-standard', ['name' => 'phone', 'label' => 'Phone'])

<div class="form-group">
    <div class="col-sm-12 text-center">
	{!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-default']) !!}
    </div>
</div>

{!! Form::close() !!}

@endsection
