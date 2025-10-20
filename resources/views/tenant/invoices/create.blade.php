@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create Invoice</h2>
        <form action="{{ route('tenant.invoices.store') }}" method="POST">
            @csrf
            <input type="text" name="total_amount" id="total_amount" value="0">
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-4">
                    <h3>Client Information</h3>
                    <input type="text" name="client_name" class="form-control" placeholder="Client Name" required>
                    <input type="text" name="client_address" class="form-control" placeholder="Client Address" required>
                </div>

                <div class="col-md-6 col-sm-12 mb-4">
                    <h3>Company Information</h3>
                    <input type="text" name="company_name" class="form-control" placeholder="Company Name"
                        value="{{ config('app.name') }}" required>
                    <input type="text" name="company_address" class="form-control" placeholder="Company Address"
                        value="{{ config('app.address') }}" required>
                    <input type="text" name="vat_number" class="form-control" placeholder="VAT Number"
                        value="{{ config('app.vat_number') }}" required>
                    <input type="date" name="issue_date" class="form-control" placeholder="Issue Date" required>
                    <input type="date" name="expiry_date" class="form-control" placeholder="Expiry Date" required>
                    <input type="text" name="invoice_number" class="form-control" placeholder="Invoice Number"
                        value="{{ $nextInvoiceNumber }}" readonly>
                </div>
            </div>

            <br>

            <h3>Items</h3>
            <div class="table-responsive">
                <table class="table table-bordered" id="items_table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>VAT Rate (%)</th>
                            <th>Amount (ZAR)</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="items[0][description]" class="form-control" required></td>
                            <td><input type="number" name="items[0][quantity]" class="form-control quantity" min="1"
                                    required></td>
                            <td><input type="number" name="items[0][unit_price]" class="form-control unit-price"
                                    step="0.01" min="0" required></td>
                            <td><input type="number" name="items[0][vat_rate]" class="form-control vat-rate" step="0.01"
                                    min="0" required></td>
                            <td><input type="number" name="items[0][amount]" class="form-control amount" step="0.01"
                                    min="0" readonly></td>
                            <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
                <div>
                    <button type="button" class="btn btn-primary" id="add_item">Add Item</button>
                    <button type="submit" class="btn btn-success">Create Invoice</button>
        </form>
    </div>
    <!-- Add this to your custom CSS for responsiveness if needed -->
    <style>
        /* Ensure the container takes full width on smaller screens */
        .container {
            max-width: 100%;
            padding: 0 15px;
        }

        /* Adjust input field widths for smaller screens */
        .form-control {
            width: 100%;
        }

        /* Optional: Custom styles for improving responsiveness */
        @media (max-width: 768px) {
            .row>.col-md-6 {
                margin-bottom: 20px;
                /* Adjust space between columns */
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let itemIndex = 1;

            // Add this function to calculate and update the total amount
            function updateTotalAmount() {
                let total = 0;
                $('.amount').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total_amount').val(total.toFixed(2));
            }

            $("#add_item").click(function() {
                $("#items_table tbody").append(`
                <tr>
                    <td><input type="text" name="items[${itemIndex}][description]" class="form-control" required></td>
                    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" min="1" required></td>
                    <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price" step="0.01" min="0" required></td>
                    <td><input type="number" name="items[${itemIndex}][vat_rate]" class="form-control vat-rate" step="0.01" min="0" required></td>
                    <td><input type="number" name="items[${itemIndex}][amount]" class="form-control amount" step="0.01" min="0" readonly></td>
                    <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
                </tr>
            `);
                itemIndex++;
                updateTotalAmount();
            });

            $(document).on("click", ".remove-item", function() {
                $(this).closest("tr").remove();
                updateTotalAmount();
            });

            $(document).on("input", ".quantity, .unit-price, .vat-rate", function() {
                let row = $(this).closest("tr");
                let quantity = parseFloat(row.find(".quantity").val()) || 0;
                let unitPrice = parseFloat(row.find(".unit-price").val()) || 0;
                let vatRate = parseFloat(row.find(".vat-rate").val()) || 0;

                let amount = quantity * unitPrice * (1 + vatRate / 100);
                row.find(".amount").val(amount.toFixed(2));
                updateTotalAmount();
            });

            updateTotalAmount();
        });
    </script>
@endsection
