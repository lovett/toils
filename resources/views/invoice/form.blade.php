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

                    @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Start', 'autofill' => true, 'pickable' => ['relmonth-start']])

                    @include('partials.formgroup-date', ['name' => 'end', 'label' => 'End', 'autofill' => true, 'pickable' => ['relmonth-end']])

                    @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name', 'autofill' => true])

                    @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary', 'autofill' => true])

                    <div class="form-group row">
                        {!! Form::label('amount', 'Amount', ['class' => 'col-sm-2 col-form-label text-right']) !!}
                        @php($fieldClasses = ['form-control'])
                        @if ($errors->has('amount'))
                            @php($fieldClasses[] = 'is-invalid')
                        @endif
                        <div class="col-sm-3">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                {!! Form::text('amount', old('amount', $model->amount), ['class' => $fieldClasses]) !!}
                                @include('partials.form-field-error', ['name' => 'amount'])
                            </div>
                            <autofill-hint target="INPUT[name=amount]" v-bind:value="suggestedAmount" v-bind:previous="previousAmount"></autofill-hint>
                        </div>
                    </div>

                    <div class="form-group row">
                        {!! Form::label('receipt', 'Receipt', ['class' => 'col-sm-2 col-form-label text-right']) !!}
                        @if ($model->receipt)
                            <div class="col-sm-2">
                                <div class="py-2 d-flex align-items-center">

                                    @php ($extension = pathinfo(strtolower($model->receipt), PATHINFO_EXTENSION))

                                    @if ($extension == "pdf")
                                        <svg class="icon file-icon"><use xlink:href="#icon-file-pdf"></use></svg>
                                    @endif

                                    @if (in_array($extension, ["png", "jpg"]))
                                        <svg class="icon file-icon"><use xlink:href="#icon-file-picture"></use></svg>
                                    @endif

                                    <a class="flex-fill" target="_blank" href="{{ route('invoice.receipt', $model->id) }}">view</a>
                                </div>
                            </div>
                        @endif

                        @php($size="col-sm-9")
                        @if ($model->receipt)
                            @php($size="col-sm-8")
                        @endif
                        <div class="{{ $size }}">
                            <div>
                                @php($fieldClass = '')
                                @if ($errors->has('receipt'))
                                    @php($fieldClass .= 'is-invalid')
                                @endif
                                <b-form-file
                                    v-model="file"
                                    placeholder="Choose a file or drop it here..."
                                    drop-placeholder="Drop file here..."
                                    id="invoice-receipt"
                                name="receipt"
                                    class="{{ $fieldClass }}"
                                ></b-form-file>
                            </div>

                            @include('partials.form-field-error', ['name' => 'receipt'])
                            @include('partials.form-field-help', ['name' => 'receipt', 'help' => __('help.max_upload_size', ['size' => $maxFileSize])])
                        </div>
                    </div>

                    @include('partials.formgroup-date', ['name' => 'sent', 'label' => 'Sent', 'pickable' => ['relday']])

                    @include('partials.formgroup-date', ['name' => 'due', 'label' => 'Due', 'autofill' => true, 'pickable' => ['relmonth-end']])

                    @include('partials.formgroup-date', ['name' => 'paid', 'label' => 'Paid', 'pickable' => ['relday']])

                    @include('partials.formgroup-date', ['name' => 'abandonned', 'label' => 'Abandonned', 'pickable' => ['relday']])

                    @include('partials.save-button')

                    {!! Form::close() !!}
                </autofill>
            </div>
        </div>
    </div>
@endsection
