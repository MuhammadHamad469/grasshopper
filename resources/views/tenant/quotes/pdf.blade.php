<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .quote-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .quote-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .quote-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .quote-box table tr td:nth-child(2) {
            text-align: right;
        }
        .quote-box table tr.top table td {
            padding-bottom: 20px;
        }
        .quote-box table tr.information table td {
            padding-bottom: 40px;
        }
        .quote-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .quote-box table tr.details td {
            padding-bottom: 20px;
        }
        .quote-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .quote-box table tr.item.last td {
            border-bottom: none;
        }
        .quote-box table tr.total td:nth-child(2) {
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
<div class="quote-box">
    <table>
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{ public_path('img/ggt-logo.png') }}" class="logo" alt="Company Logo">
                        </td>
                        <td>
                            <h2>QUOTE</h2>
                            <p>Quote Number: {{ $quote->quote_number }}</p>
                            <p>Quote Date: {{ $quote->issue_date->format('j M Y') }}</p>
                            <p>Due Date: {{ $quote->expiry_date->format('j M Y') }}</p>
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
                            {{ $quote->client_name }}<br>
                            {{ $quote->client_address }}
                        </td>
                        <td>
                            VAT Number: {{ $quote->vat_number }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td>Description</td>
            <td>Amount</td>
        </tr>
        @foreach($quote->items as $item)
            <tr class="item">
                <td>{{ $item->description }} (Quantity: {{ $item->quantity }})</td>
                <td>{{ number_format($item->amount, 2) }} ZAR</td>
            </tr>
        @endforeach
        <tr class="total">
            <td></td>
            <td>Total: {{ number_format($quote->total_amount, 2) }} ZAR</td>
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
                            Reference: Quote number
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>Company Registration No: 2010/022893/23. Registered Office: 10 Stuart Cl, Somerset West Business Park, Western Cape, 7130, South Africa.</p>
        <p>&copy; {{ date('Y') }} {{ $quote->company_name }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>