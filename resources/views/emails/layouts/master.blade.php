<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <style type="text/css">
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0 !important;
            padding: 0 !important;
            background: #f5f5f5;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background: #f5f5f5;
            padding: 40px 0;
        }

        .main {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .header {
            padding: 40px 30px;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
            border-bottom: 1px solid orange;
        }

        .content {
            padding: 40px 30px;
        }

        .footer {
            padding: 30px 20px;
            background: #34495e;
            border-radius: 0 0 8px 8px;
            color: #ffffff;
            text-align: center;
        }

        .text {
            margin: 0 0 20px;
            font-size: 16px;
            color: #34495e;
            line-height: 1.6;
        }

        .text--highlight {
            color: #ffaa00;
        }

        .heading-1 {
            margin: 0 0 25px;
            font-size: 24px;
            color: #34495e;
            text-align: center;
        }

        .details-box {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #4b8da0;
        }

        .details-heading {
            margin: 0 0 15px;
            font-size: 18px;
            color: #216592;
        }

        .button {
            background: #4a974e;
            color: #ffffff !important;
            padding: 14px 30px;
            border-radius: 6px;
            text-decoration: none !important;
            display: inline-block;
            font-weight: bold;
        }

        .button-container {
            text-align: center;
            margin: 35px 0;
        }

        @media screen and (max-width: 620px) {
            .wrapper {
                padding: 0 !important;
            }
            .main {
                width: 100% !important;
                border-radius: 0 !important;
            }
            .header, .content {
                padding: 25px 20px !important;
            }
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <table class="main" role="presentation">
        <tr>
            <td class="header" align="center">
                <img src="{{ $message->embed(public_path('img/ggt-logo.png')) }}"
                     alt="Company Logo"
                     style="width: 200px; height: auto;">
            </td>
        </tr>

        <tr>
            <td class="content">
                <h1 class="heading-1">@yield('title')</h1>
                @yield('content')
                <p class="text">Sincerely,<br>The Grasshopper Team</p>
            </td>
        </tr>

        <tr>
            <td class="footer">
                <p >© {{ date('Y') }} Grasshopper Tech. All rights reserved.</p>

                <a href="https://www.linkedin.com/company/grasshopper-green-technology/" style="text-decoration: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#308638">
                        <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/grasshopper_green_technologies/" style="text-decoration: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="#308638">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            </td>
        </tr>
    </table>
</div>
</body>
</html>