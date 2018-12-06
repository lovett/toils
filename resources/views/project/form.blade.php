@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this project', 'route' => 'project.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="card">
            <div class="card-body">

                {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method]) !!}

                @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

                @include('partials.formgroup-menu', ['name' => 'client_id', 'label' => 'Client', 'items' => $clients, 'selectedItem' => $model->client_id])

                @include('partials.formgroup-standard', ['name' => 'allottedTotalHours', 'label' => 'Total Hours'])

                @include('partials.formgroup-standard', ['name' => 'allottedWeeklyHours', 'label' => 'Weekly Hours'])


                @include('partials.formgroup-checkbox', ['name' => 'active', 'label' => 'This project is active', 'checked' => $model->active])

                @include('partials.formgroup-checkbox', ['name' => 'billable', 'label' => 'This project is billable', 'checked' => $model->billable])

                @include('partials.formgroup-checkbox', ['name' => 'taxDeducted', 'label' => 'Tax will be deducted', 'checked' => $model->taxDeducted])

                @include('partials.save-button')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
