@extends('layouts.master')


@section('page_main')
    @include('partials.search', ['route' => 'invoice.index', 'search' => $search, 'fields' => $searchFields])
    <table class="table">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Client</th>
                <th>Project</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $invoice)
                <tr>
                    <td>
                        <a href="{{ route('invoice.edit', $invoice) }}">
                            {{ $invoice->name }}
                            <div class="small">
                                {{ TimeHelper::dateFromRaw($invoice->start) }} to
                                {{ TimeHelper::dateFromRaw($invoice->end) }}
                            </div>
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
