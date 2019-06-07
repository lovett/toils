@extends('layouts.app')

@section('page_main')
    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this invoice', 'route' => 'invoice.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="card">
            <div class="card-body">

                @include('partials.error-alert')

                <autofill
                    inline-template
                    url="{{ route('invoice.suggestByProject') }}"
                    fields="start end name summary amount due paid"
                    v-bind:autofetch="{{ ($model->project_id > 0)? 'true' : 'false' }}"
                    v-bind:enabled="{{ Route::is('invoice.edit') ? 'false' : 'true' }}"
                >

                    {!! Form::model($model, ['files' => true, 'route' => $submission_route, 'method' => $submission_method, 'class' => 'invoice-form']) !!}

                    @isset($client)
                    @include('partials.project-menu-filtered', ['client' => $client])
                    @endisset

                    @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id, 'vchange' => 'fetch'])

                    @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Start', 'autofill' => true, 'pickable' => ['relday', 'month', 'day', 'year']])

                    @include('partials.formgroup-date', ['name' => 'end', 'label' => 'End', 'autofill' => true, 'pickable' => ['relday', 'month', 'day', 'year']])

                    @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name', 'autofill' => true])

                    @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary', 'autofill' => true])

                    <div class="form-group row">
                        {!! Form::label('amount', 'Amount', ['class' => 'col-sm-2 col-form-label text-right']) !!}
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                {!! Form::text('amount', old('amount', $model->amount), ['class' => 'form-control']) !!}
                            </div>
                            <autofill-hint target="INPUT[name=amount]" v-bind:value="suggestedAmount" v-bind:previous="previousAmount"></autofill-hint>
                        </div>

                        {!! Form::label('receipt', 'Receipt', ['class' => 'col-sm-1 col-form-label']) !!}
                        <div class="col-sm-3">
                            @php($fieldClasses = ['custom-file-input'])
                            @if ($errors->has('receipt'))
                                @php($fieldClasses[] = 'is-invalid')
                            @endif
                            {!! Form::file('receipt', ['id' => 'invoice-receipt', 'class' => $fieldClasses]) !!}
                            <label class="custom-file-label" for="invoice-receipt">Choose file</label>
                            @include('partials.form-field-error', ['name' => 'receipt'])
                            @include('partials.form-field-help', ['name' => 'receipt', 'help' => __('help.max_upload_size', ['size' => $maxFileSize])])
                        </div>

                        @if ($model->receipt)
                            <div class="col-sm-3 text-right">
                                <svg class="icon active"><use xlink:href="#icon-coin-dollar"></use></svg>
                                <a target="_blank" href="{{ route('invoice.receipt', $model->id) }}">view existing receipt</a>
                            </div>
                        @endif
                    </div>

                    @include('partials.formgroup-date', ['name' => 'sent', 'label' => 'Sent', 'pickable' => ['relday', 'month', 'day', 'year']])

                    @include('partials.formgroup-date', ['name' => 'due', 'label' => 'Due', 'autofill' => true, 'pickable' => ['month', 'day', 'year']])

                    @include('partials.formgroup-date', ['name' => 'paid', 'label' => 'Paid', 'autofill' => false, 'pickable' => ['relday', 'month', 'day', 'year']])

                    @include('partials.save-button')

                    {!! Form::close() !!}
                </autofill>
            </div>
        </div>
    </div>
@endsection
