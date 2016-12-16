@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'invoice.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Client</th>
                <th>Project</th>
                <th>Date</th>
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
                        <a href="{{ route('client.show', ['record' => $invoice->project->client]) }}">
                            {{ $invoice->project->client->name }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('project.show', ['record' => $invoice->project]) }}">
                            {{ $invoice->project->name }}
                        </a>
                    </td>
                    <td>
                        {{ TimeHelper::dateFromRaw($invoice->start) }} - {{ TimeHelper::dateFromRaw($invoice->end) }}
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
    <script src="{{ asset('js/vue.min.js') }}"></script>
    <script src="{{ asset('js/searchby.js') }}"></script>
@endsection
