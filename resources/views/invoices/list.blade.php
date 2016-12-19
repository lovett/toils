@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'invoice.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Client</th>
                <th>Project</th>
                <th>Time</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>
                        {{ $invoice->number }}
                    </td>
                    <td>
                        <a href="{{ route('invoice.edit', $invoice) }}">
                            {{ $invoice->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('client.show', ['record' => $invoice->clientId]) }}">
                            {{ $invoice->clientName }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('project.show', ['record' => $invoice->projectId]) }}">
                            {{ $invoice->projectName }}
                        </a>
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($invoice->start) }} - {{ TimeHelper::dateFromRaw($invoice->end) }}
                        <p class="small">
                            {{ TimeHelper::hoursAndMinutes($invoice->totalMinutes) }}
                        </p>
                    </td>
                    <td class="text-right">
                        {{ CurrencyHelper::withSymbol($invoice->amount) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <nav>
        {!! $invoices->render() !!}
    </nav>
@endsection

@section('nav_primary')
    {!! link_to_route('invoice.create', 'Add an invoice') !!}
@endsection

@section('page_scripts')
    @include('partials.vue')
    <script src="{{ asset('js/searchby.js') }}"></script>
@endsection
