@extends('layouts.master')

@section('page_main')

<div class="container">

{!! Form::model($client, ['route' => 'clients::store', 'class' => 'form-horizontal']) !!}

<div class="form-group">
    {!! Form::label('name', 'Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	{!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
	<div class="checkbox">
	    <label>
		{!! Form::checkbox('active', 1, true) !!}
		This client is active
	    </label>
	</div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('contact_name', 'Contact Name', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	{!! Form::text('contact_name', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('contact_email', 'Contact Email', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	{!! Form::text('contact_email', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('address1', 'Mailing Address', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	{!! Form::text('address1', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
	{!! Form::text('address2', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('city', 'City', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	{!! Form::text('city', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('locality', 'State', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-1">
	{!! Form::text('locality', null, ['class' => 'form-control', 'maxlength' => 2]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('postal_code', 'Zip', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-2">
	{!! Form::text('postal_code', null, ['class' => 'form-control', 'maxlength' => 5]) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('phone', 'Phone', ['class' => 'col-sm-2 control-label']) !!}
    <div class="col-sm-10">
	{!! Form::text('phone', null, ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
	{!! Form::button('submit', ['type' => 'submit', 'class' => 'btn btn-default']) !!}
    </div>
</div>
{!! Form::close() !!}
</div>
@endsection
