@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this invoice', 'route' => 'invoice.destroy', 'model' => $model])
    @endif

    <div class="container">
    <autofill inline-template url="{{ route('invoice.suggestByProject') }}" fields="start, end, name, summary, amount, due">

        {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'invoice-form form-horizontal']) !!}

        @isset($client)
        @include('partials.project-menu-filtered', ['client' => $client])
        @endisset

        @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id, 'vchange' => 'fetch'])

        @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Start', 'autofill' => true])

        @include('partials.formgroup-date', ['name' => 'end', 'label' => 'End', 'autofill' => true])

        @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name', 'autofill' => true])

        @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary', 'autofill' => true])

        <div class="form-group">
            {!! Form::label('amount', 'Amount', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-3">
                <div class="input-group">
                    <div class="input-group-addon">$</div>
                    {!! Form::text('amount', $model->amount, ['class' => 'form-control']) !!}
                </div>
                <autofill-hint target="INPUT[name=amount]" v-bind:value="suggestedAmount" v-bind:previous="previousAmount"></autofill-hint>
            </div>

            {!! Form::label('receipt', 'Receipt', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-2">
                <input type="file" id="receipt" />
            </div>
        </div>

        @include('partials.formgroup-date', ['name' => 'sent', 'label' => 'Sent'])

        @include('partials.formgroup-date', ['name' => 'due', 'label' => 'Due', 'autofill' => true])

        @include('partials.save-button')

        {!! Form::close() !!}
    </autofill>
    </div>
@endsection
