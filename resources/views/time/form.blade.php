@extends('layouts.app')

@section('page_main')

    @if ($model->id)
        @include('partials.delete', ['label' => 'Delete this entry', 'route' => 'time.destroy', 'model' => $model])
    @endif

    <div class="container">
        <div class="card">
            <div class="card-body">

                @include('partials.error-alert')


                <autofill
                    inline-template
                    url="{{ route('time.suggestByProject') }}"
                    fields="estimatedDuration start"
                    v-bind:autofetch="{{ ($model->project_id > 0)? 'true' : 'false' }}"
                    v-bind:enabled="{{ Route::is('time.edit') ? 'false' : 'true' }}"
                >

                    {!! Form::model($model, ['route' => $submission_route, 'method' => $submission_method]) !!}

                    @isset($client)
                    @include('partials.project-menu-filtered', ['client' => $client])
                    @endisset

                    @include('partials.formgroup-menu', ['name' => 'project_id', 'label' => 'Project', 'items' => $projects, 'selectedItem' => $model->project_id, 'vchange' => 'fetch', 'readonly' => $projectIsReadOnly])

                    @include('partials.formgroup-standard', ['name' => 'estimatedDuration', 'label' => 'Time Estimate', 'inlineSuffix' => 'minutes', 'size' => 2, 'autofill' => true])

                    @include('partials.formgroup-date', ['name' => 'start', 'label' => 'Date', 'autofill' => true, 'pickable' => ['relday']])

                    @include('partials.formgroup-time', ['name' => 'start', 'fieldName' => 'startTime', 'label' => 'Start', 'pickable' => ['reltime', 'time']])

                    @include('partials.formgroup-time', ['name' => 'end', 'fieldName' => 'endTime', 'label' => 'End', 'pickable' => ['reltime', 'time']])

                    @include('partials.formgroup-textarea', ['name' => 'summary', 'label' => 'Summary'])

                    @include('partials.formgroup-standard', ['name' => 'tagList', 'label' => 'Tags'])

                    @include('partials.formgroup-checkbox', ['name' => 'billable', 'label' => 'This entry is billable', 'checked' => $model->billable])

                    @include('partials.save-button')

                    {!! Form::close() !!}
                </autofill>
            </div>
        </div>
    </div>
@endsection
