@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this invoice', 'route' => 'invoice.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">
                <autofill
                    inline-template
                    url="{{ route('invoice.suggestByProject') }}"
                    fields="start end name summary amount due paid"
                    v-bind:autofetch="{{ ($model->project_id > 0)? 'true' : 'false' }}"
                    v-bind:enabled="{{ Route::is('invoice.edit') ? 'false' : 'true' }}"
                >

                    {!! Form::model($model, ['files' => true, 'route' => $submission_route, 'method' => $submission_method, 'class' => 'invoice-form form-horizontal']) !!}

                    @isset($client)
                    @include('partials.project-menu-filtered', ['client' => $client])
                    @endisset

                    @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id, 'vchange' => 'fetch'])

                    @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Start', 'autofill' => true, 'pickable' => ['relday', 'relweek', 'month', 'day', 'year']])

                    @include('partials.formgroup-date', ['name' => 'end', 'label' => 'End', 'autofill' => true, 'pickable' => ['relday', 'relweek', 'month', 'day', 'year']])

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
                        <div class="col-sm-3">
                            {!! Form::file('receipt') !!}
                        </div>

                        @if ($model->receipt)
                            <div class="col-sm-3 text-right">
                                <a href="{{ route('invoice.receipt', $model->id) }}">view existing receipt</a>
                            </div>
                        @endif
                    </div>

                    @include('partials.formgroup-date', ['name' => 'sent', 'label' => 'Sent', 'pickable' => ['relday', 'month', 'day', 'year']])

                    @include('partials.formgroup-date', ['name' => 'due', 'label' => 'Due', 'autofill' => true, 'pickable' => ['month', 'day', 'year']])

                    @include('partials.formgroup-date', ['name' => 'paid', 'label' => 'Paid', 'autofill' => false])

                    @include('partials.save-button')

                    {!! Form::close() !!}
                </autofill>
            </div>
        </div>
    </div>
@endsection
