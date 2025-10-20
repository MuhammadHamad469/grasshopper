@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Quote Details</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
                <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('tenant.quotes.pdf', $quote) }}" target="blank" class="btn btn-secondary w-100">Download PDF</a>
                        </div>
                        <form action="{{ route('tenant.tenant.quotes.generateInvoice', $quote) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Generate Invoice</button>
                        </form>
                    </div>
        <div class="row">
            <div class="col-md-6">
                <h3>Client Information</h3>
                <p><strong>Name:</strong> {{ $quote->client_name }}</p>
                <p><strong>Address:</strong> {{ $quote->client_address }}</p>
            </div>

            <div class="col-md-6">
                <h3>Company Information</h3>
                <p><strong>Name:</strong> {{ $quote->company_name }}</p>
                <p><strong>Address:</strong> {{ $quote->company_address }}</p>
                <p><strong>VAT Number:</strong> {{ $quote->vat_number }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Quote Details</h3>
                <p><strong>Quote Number:</strong> {{ $quote->quote_number }}</p>
                <p><strong>Issue Date:</strong> {{ $quote->issue_date->format('Y-m-d') }}</p>
                <p><strong>Expiry Date:</strong> {{ $quote->expiry_date->format('Y-m-d') }}</p>
            </div>
        </div>

        <h3>Items</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>VAT Rate</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            @foreach($quote->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->vat_rate, 2) }}%</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Amount:</th>
                <th>{{ number_format($quote->total_amount, 2) }}</th>
            </tr>
            </tfoot>
        </table>

        <a href="{{ route('tenant.quotes.edit', $quote) }}" class="btn btn-primary">Edit Quote</a>
        <a href="{{ route('tenant.quotes.index') }}" class="btn btn-secondary">Back to Quotes</a>
    </div>
@endsection