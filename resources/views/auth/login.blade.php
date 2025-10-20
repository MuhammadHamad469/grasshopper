<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('quickadmin.qa_login') }}</title>
    <style>
        :root {
            --primary-color: #34495e;
            --secondary-color: #4b8da0;
            --text-color: #000000;
            --bg-gradient: linear-gradient(to right, #216592, #4b8da0, #34495e);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Tahoma, sans-serif;
        }

        body {
            line-height: 1.6;
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: var(--bg-gradient);
        }

        .container {
            padding: 10px 10%;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.5rem;
            color: var(--text-color);
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: var(--secondary-color);
        }

        .form-box {
            position: relative;
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            min-width: 450px;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: black;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.3s ease;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
        }

        p {
            margin-top: 1rem;
            text-align: center;
        }

        a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: var(--secondary-color);
        }

        .alert {
            background-color: #dd4b39 !important;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px 5%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-box login-box">
            <button class="close-btn" onclick="window.location.href='{{ route('landing') }}'">×</button>
            <h2>{{ __('Welcome Back') }}</h2>

            {{-- @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>@lang('quickadmin.qa_whoops')</strong> @lang('quickadmin.qa_there_were_problems_with_input'):
                    <br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="border:1px solid red; padding:15px; border-radius:5px;">
                    <strong>Whoops!</strong> There were problems with input:
                    <ul style="list-style-type:disc; list-style-position:inside; margin:0; line-height:1.2;">
                        @foreach ($errors->all() as $error)
                            <li style="margin:0;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif




            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="input-group">
                    <label for="email">@lang('quickadmin.qa_email')</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="Enter your email">
                </div>

                <div class="input-group">
                    <label for="password">@lang('quickadmin.qa_password')</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <div class="input-group">
                    <label>
                        <input type="checkbox" name="remember"> @lang('quickadmin.qa_remember_me')
                    </label>
                </div>

                <button type="submit" class="btn">@lang('quickadmin.qa_login')</button>
            </form>

            <p>
                <a href="{{ route('auth.password.reset') }}">@lang('quickadmin.qa_forgot_password')</a>
            </p>
        </div>
    </div>
</body>

</html>
