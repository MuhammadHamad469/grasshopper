@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Invoice Details</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <a href="{{ route('tenant.invoices.pdf', $invoice) }}" target="blank" class="btn btn-secondary">Download PDF</a>
        <div class="row">
            <div class="col-md-6">
                <h3>Client Information</h3>
                <p><strong>Name:</strong> {{ $invoice->client_name }}</p>
                <p><strong>Address:</strong> {{ $invoice->client_address }}</p>
            </div>

            <div class="col-md-6">
                <h3>Company Information</h3>
                <p><strong>Name:</strong> {{ $invoice->company_name }}</p>
                <p><strong>Address:</strong> {{ $invoice->company_address }}</p>
                <p><strong>VAT Number:</strong> {{ $invoice->vat_number }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Invoice Details</h3>
                <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Issue Date:</strong> {{ $invoice->issue_date->format('Y-m-d') }}</p>
                <p><strong>Expiry Date:</strong> {{ $invoice->expiry_date->format('Y-m-d') }}</p>
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
            @foreach($invoice->items as $item)
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
                <th>{{ number_format($invoice->total_amount, 2) }}</th>
            </tr>
            </tfoot>
        </table>

        <a href="{{ route('tenant.invoices.edit', $invoice) }}" class="btn btn-primary">Edit Invoice</a>
        <a href="{{ route('tenant.invoices.index') }}" class="btn btn-secondary">Back to Invoices</a>
    </div>
@endsection