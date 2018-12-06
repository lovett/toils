@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this estimate', 'route' => 'estimate.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="card">
            <div class="card-body">

                @include('partials.error-alert')

                {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method]) !!}

                @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

                @include('partials.formgroup-date', ['name' => 'submitted', 'label' => 'Submission Date', 'pickable' => ['month', 'day', 'year']])

                @include('partials.formgroup-date', ['name' => 'closed', 'label' => 'Close Date', 'pickable' => ['month', 'day', 'year']])

                @include('partials.formgroup-menu', ['name' => 'status', 'label' => 'Status', 'items' => $statuses, 'selectedItem' => $model->status])

                @include('partials.formgroup-standard', ['name' => 'recipient', 'label' => 'Recipient'])

                @include('partials.formgroup-menu', ['name' => 'client_id', 'label' => 'Client', 'items' => $clients, 'selectedItem' => $model->client_id])

                <div class="form-group row">
                    {!! Form::label('fee', 'Fee', ['class' => 'col-sm-2 col-form-label text-right']) !!}
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            {!! Form::text('fee', $model->fee, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    {!! Form::label('hours', 'Hours', ['class' => 'col-sm-1 col-form-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('hours', $model->hours, ['class' => 'form-control']) !!}
                    </div>
                </div>

                @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary'])


                @include('partials.save-button')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
