@extends('layouts.master')

@section('page_main')

    @include('partials.error-alert')

    <autofill inline-template url="{{ route('invoices.suggestByProject') }}" fields="start, end, name, summary, amount, sent, due">

        {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'invoice-form form-horizontal']) !!}

        @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id])

        @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Start', 'ranges' => TimeHelper::ranges(), 'autofill' => true])

        @include('partials.formgroup-date', ['name' => 'end', 'label' => 'End', 'ranges' => TimeHelper::ranges(), 'autofill' => true])

        @include('partials.formgroup-standard', ['name' => 'name', 'label' => 'Name', 'autofill' => true])

        @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary', 'autofill' => true])

        <div class="form-group">
            {!! Form::label('amount', 'Amount', ['class' => 'col-sm-2 control-label']) !!}
            <div class="col-sm-3">
                <div class="input-group">
                    <div class="input-group-addon">$</div>
                    {!! Form::text('amount', $model->amount, ['class' => 'form-control']) !!}
                </div>
                <autofill-hint target="INPUT[name=amount]" v-bind:value="amount"></autofill-hint>
            </div>

            {!! Form::label('receipt', 'Receipt', ['class' => 'col-sm-1 control-label']) !!}
            <div class="col-sm-2">
                <input type="file" id="receipt" />
            </div>
        </div>

        @include('partials.formgroup-date', ['name' => 'sent', 'label' => 'Sent', 'ranges' => TimeHelper::ranges(), 'autofill' => true])

        @include('partials.formgroup-date', ['name' => 'due', 'label' => 'Due', 'ranges' => TimeHelper::ranges(), 'autofill' => true])


        <div class="form-group">
            <div class="col-sm-12 text-center">
	            {!! Form::button('Save', ['type' => 'submit', 'class' => 'btn btn-default']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </autofill>

@endsection

@section('nav_primary')
    {!! link_to($backUrl, 'Cancel') !!}
@endsection

@section('nav_supplemental')

    @if ($model->id)
        {!! Form::model($model, ['route' => ['invoice.destroy', $model->id], 'method' => 'DELETE']) !!}
        {!! Form::button('Delete', ['type' => 'submit', 'class' => 'btn btn-sm btn-danger']) !!}
        {!! Form::close() !!}
    @endif

@endsection

@section('page_scripts')
    @include('partials.vue')
    <script src="{{ asset('js/pickable.js') }}"></script>
    <script src="{{ asset('js/autofill-hint.js') }}"></script>
    <script src="{{ asset('js/autofill.js') }}"></script>
    @include('partials.vue-init');
    @include('partials.select2')
@endsection
