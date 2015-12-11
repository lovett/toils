@extends('layouts.master')

@section('page_main')

{!! Form::model($client, ['route' => 'clients::store']) !!}

<div class="field">
    {!! Form::label('name', 'Name') !!}
    {!! Form::text('name') !!}
</div>

<div class="field">
    {!! Form::checkbox('active', 1, true) !!}
    {!! Form::label('active', 'This client is active') !!}
</div>

<div class="field">
    {!! Form::label('contact_name', 'Contact Name') !!}
    {!! Form::text('contact_name') !!}
</div>

<div class="field">
    {!! Form::label('contact_email', 'Contact Email') !!}
    {!! Form::text('contact_email') !!}
</div>

<div class="field">
    {!! Form::label('address1', 'Mailing Address') !!}
    {!! Form::text('address1') !!}
</div>

<div class="field">
    {!! Form::label('address2', 'Mailing Address Line 2') !!}
    {!! Form::text('address2') !!}
</div>

<div class="field">
    {!! Form::label('city', 'City') !!}
    {!! Form::text('city') !!}
</div>

<div class="field">
    {!! Form::label('locality', 'State') !!}
    {!! Form::text('locality') !!}
</div>

<div class="field">
    {!! Form::label('postal_code', 'Zip') !!}
    {!! Form::text('postal_code') !!}
</div>

<div class="field">
    {!! Form::label('phone', 'Phone') !!}
    {!! Form::text('phone') !!}
</div>

<div class="field">
    {!! Form::submit() !!}
</div>

{!! Form::close() !!}
@endsection
