@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this client', 'route' => 'client.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">

                @include('partials.error-alert')

                {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

                @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

                @include('partials.formgroup-standard', ['name' => 'contactName', 'label' => 'Contact Name'])

                @include('partials.formgroup-standard', ['name' => 'contactEmail', 'label' => 'Contact Email'])

                @include('partials.formgroup-standard', ['name' => 'address1', 'label' => 'Mailing Address'])

                @include('partials.formgroup-standard', ['name' => 'address2', 'label' => null])

                @include('partials.formgroup-standard', ['name' => 'city', 'label' => 'City'])

                @include('partials.formgroup-standard', ['name' => 'locality', 'label' => 'State'])

                @include('partials.formgroup-standard', ['name' => 'postalCode', 'label' => 'Zip'])

                @include('partials.formgroup-standard', ['name' => 'phone', 'label' => 'Phone'])

                @include('partials.formgroup-checkbox', ['name' => 'active', 'label' => 'This client is active', 'checked' => $model->active])

                @include('partials.save-button')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
