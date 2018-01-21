@extends('layouts.app')

@section('page_main')
    <div class="container">
        <h1>{{ $model->name }}</h1>

        <p>@include('partials.active', ['value' => $model->active])</p>

        @if ($model->contactName)
            {{ $model->contactName }}
            <address>
                <a href="mailto:{{ $model->contactEmail }}">{{ $model->contactEmail }}</a>
                <div>
                    {{ AddressHelper::mailingAddress($model) }}
                </div>
                <div>
                    {!! AddressHelper::phoneUrl($model->phone) !!}
                </div>
            </address>
        @endif

        <div class="row">
            <div class="col-sm-4">
                <h2>Active Projects</h2>

                @include('partials.empty-message', ['collection' => $model->projects->where('active', true)])

                @foreach ($model->projects->where('active', true) as $project)
                    <p>
                        <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
                    </p>
                @endforeach
            </div>

            <div class="col-sm-4">
                <h2>Inactive Projects</h2>

                @include('partials.empty-message', ['collection' => $model->projects->where('active', false)])

                @foreach ($model->projects->where('active', false) as $project)
                    <p>
                        <a href="{{ route('project.show', ['project' => $project]) }}">{{ $project->name }}</a>
                    </p>
                @endforeach
            </div>

            <div class="col-sm-4">
                <h2>Recent Invoices</h2>

                @include('partials.empty-message', ['collection' => $invoices])

                <table class="table">
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ TimeHelper::dateFromRaw($invoice->sent) }}</td>
                            <td>{{ CurrencyHelper::withSymbol($invoice->amount) }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <h2>Time</h2>
        @include('partials.empty-message', ['collection' => $time])

        @include('time.table', ['collection' => $time]);
    </div>

    @include('partials.timestamps-footer', ['record' => $model])
@endsection

@section('subnav_supplemental')
    <li>{!! link_to_route('project.create', 'New project', ['client' => $model->id]) !!}</li>
@endsection
