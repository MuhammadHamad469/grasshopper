<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .logo {
            max-width: 260px;
            max-height: 265px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<div class="invoice-box">
    <table>
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{ public_path('img/ggt-logo.png') }}" class="logo" alt="Company Logo">
                        </td>
                        <td>
                            <h2>INVOICE</h2>
                            <p>Invoice Number: {{ $invoice->invoice_number }}</p>
                            <p>Invoice Date: {{ $invoice->issue_date->format('j M Y') }}</p>
                            <p>Due Date: {{ $invoice->expiry_date->format('j M Y') }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            {{ $invoice->client_name }}<br>
                            {{ $invoice->client_address }}
                        </td>
                        <td>
                            VAT Number: {{ $invoice->vat_number }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td>Description</td>
            <td>Amount</td>
        </tr>
        @foreach($invoice->items as $item)
            <tr class="item">
                <td>{{ $item->description }} (Quantity: {{ $item->quantity }})</td>
                <td>{{ number_format($item->amount, 2) }} ZAR</td>
            </tr>
        @endforeach
        <tr class="total">
            <td></td>
            <td>Total: {{ number_format($invoice->total_amount, 2) }} ZAR</td>
        </tr>
    
        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <strong>Bank Details:</strong><br>
                            Bank: FNB<br>
                            Account number: 63100059781<br>
                            Account type: GOLD BUSINESS ACCOUNT<br>
                            Branch code: 255355<br>
                            Reference: Invoice number
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Company Registration No: 2010/022893/23. Registered Office: 10 Stuart Cl, Somerset West Business Park, Western Cape, 7130, South Africa.</p>
        <p>&copy; {{ date('Y') }} {{ $invoice->company_name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>