<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forbidden!</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #086177;
        }

        .container {
            text-align: center;
            padding: 20px;
            border: 1px solid #2a89a6;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #fffdfd;
            font-size: 2em;
            margin-bottom: 20px;
        }

        p {
            color: #000000;
            margin-bottom: 15px;
        }

        .robot {
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            background-color: #ddd;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .robot-head {
            width: 80px;
            height: 80px;
            background-color: #fff;
            border-radius: 50%;
            position: relative;
        }

        .robot-antenna {
            width: 10px;
            height: 20px;
            background-color: #427998;
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
        }

        .robot-eyes {
            width: 10px;
            height: 5px;
            background-color: #000;
            border-radius: 2px;
            position: absolute;
            top: 30px;
            left: 35%;
        }

        .robot-eyes:after {
            content: "";
            display: block;
            width: 10px;
            height: 5px;
            background-color: #000;
            border-radius: 2px;
            position: absolute;
            top: 0;
            left: 40px;
        }

        .robot-mouth {
            width: 30px;
            height: 5px;
            background-color: #000;
            position: absolute;
            top: 45px;
            left: 40%;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Forbidden!</h1>
    <p>You don't have permission to access this page.</p>
    <p>Maybe try going back to the <a href="/">homepage</a>?</p>

    <div class="robot">
        <div class="robot-head">
            <div class="robot-antenna"></div>
            <div class="robot-eyes"></div>
            <div class="robot-mouth"></div>
        </div>
    </div>
</div>

</body>
</html>