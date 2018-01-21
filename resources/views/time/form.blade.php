@extends('layouts.app')

@section('page_main')

    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this entry', 'route' => 'time.destroy', 'model' => $model])
    @endif

    @if (isset($previousModel))
        <div class="container text-right">
            <a href="#" class="btn btn-sm btn-info"
               onclick="prefill(event)"
                href="#"
                data-project_id="{{ $previousModel->project_id }}"
	            data-estimate="{{ $previousModel->estimatedDuration }}"
	            data-summary="{{ $previousModel->summary}}"
            >Prefill</a>
        </div>
    @endif

    <div class="container">
        {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

        @isset($client)
        @include('partials.project-menu-filtered', ['client' => $client])
        @endisset

        @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id])

        @include('partials.formgroup-standard', ['name' => 'estimatedDuration', 'label' => 'Estimate'])

        @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Date'])

        @include('partials.formgroup-time', ['name' => 'start', 'fieldName' => 'startTime', 'label' => 'Start'])
        @include('partials.formgroup-time', ['name' => 'end', 'fieldName' => 'endTime', 'label' => 'End'])

        @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary'])

        @include('partials.save-button')

        {!! Form::close() !!}
    </div>
@endsection
