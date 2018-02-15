@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this estimate', 'route' => 'estimate.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">

                @include('partials.error-alert')

                {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

                @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name'])

                @include('partials.formgroup-date', ['name' => 'submitted', 'label' => 'Submission Date'])

                @include('partials.formgroup-date', ['name' => 'closed', 'label' => 'Close Date'])

                @include('partials.formgroup-menu', ['name' => 'status', 'label' => 'Status', 'items' => $statuses, 'selectedItem' => $model->status])

                @include('partials.formgroup-standard', ['name' => 'recipient', 'label' => 'Recipient'])

                @include('partials.formgroup-menu', ['name' => 'client_id', 'label' => 'Client', 'items' => $clients, 'selectedItem' => $model->client_id])

                <div class="form-group">
                    {!! Form::label('fee', 'Fee', ['class' => 'col-sm-2 control-label']) !!}
                    <div class="col-sm-3">
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            {!! Form::text('fee', $model->fee, ['class' => 'form-control']) !!}
                        </div>
                    </div>

                    {!! Form::label('hours', 'Hours', ['class' => 'col-sm-1 control-label']) !!}
                    <div class="col-sm-2">
                        {!! Form::text('hours', $model->hours) !!}
                    </div>
                </div>

                @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary', 'autofill' => true])


                @include('partials.save-button')

                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
