@extends('layouts.app')

@section('page_main')

    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this entry', 'route' => 'time.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-body">
o                <autofill
                    inline-template
                    url="{{ route('time.suggestByProject') }}"
                    fields="estimatedDuration start summary"
                    v-bind:autofetch="{{ ($model->project_id > 0)? 'true' : 'false' }}"
                >

                    {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method, 'class' => 'form-horizontal']) !!}

                    @isset($client)
                    @include('partials.project-menu-filtered', ['client' => $client])
                    @endisset

                    @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id, 'vchange' => 'fetch'])

                    @include('partials.formgroup-standard', ['name' => 'estimatedDuration', 'label' => 'Estimate', 'autofill' => true])

                    @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Date', 'autofill' => true, 'pickable' => ['relday', 'month', 'day', 'year']])

                    @include('partials.formgroup-time', ['name' => 'start', 'fieldName' => 'startTime', 'label' => 'Start', 'pickable' => ['time']])
                    @include('partials.formgroup-time', ['name' => 'end', 'fieldName' => 'endTime', 'label' => 'End', 'pickable' => ['time']])

                    @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary', 'autofill' => true])

                    @include('partials.save-button')

                    {!! Form::close() !!}
                </autofill>
            </div>
        </div>
    </div>
@endsection
