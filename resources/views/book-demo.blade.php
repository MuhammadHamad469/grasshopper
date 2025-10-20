<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Book a Demo') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            background: var(--bg-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            position: relative;
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

        h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--primary-color);
        }

        .demo-form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: var(--secondary-color);
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .submit-btn {
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: var(--text-color);
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .submit-btn:hover {
            background-color: var(--primary-color);
            color: #fff;
            transform: translateY(-2px);
        }

        .success-message {
            display: none;
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            text-align: center;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        @media (max-width: 768px) {
            body {
                padding: 0.5rem;
            }

            .container {
                padding: 1.5rem;
                border-radius: 5px;
            }

            h1 {
                font-size: 1.75rem;
            }

            input, textarea, .submit-btn {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            input, textarea, .submit-btn {
                font-size: 0.8rem;
                padding: 0.6rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <button class="close-btn" onclick="window.location.href='{{ route('landing') }}'">×</button>
    <h1>{{ __('Book a Demo') }}</h1>
    <form id="demoForm" class="demo-form" action="https://formsubmit.co/info@grasshoppertech.co.za" method="POST">
    <!-- FormSubmit hidden fields -->
    <input type="hidden" name="_captcha" value="true">
    <input type="hidden" name="_template" value="box">
    <input type="hidden" name="_next" value="https://app.grasshoppertech.co.za">
    <input type="hidden" name="_subject" value="New Demo Request!">

    <label for="name">{{ __('Name:') }}</label>
    <input type="text" id="name" name="name" placeholder="{{ __('Enter your name') }}" required>

    <label for="company">{{ __('Company Name:') }}</label>
    <input type="text" id="company" name="company" placeholder="{{ __('Enter your company name') }}" required>

    <label for="email">{{ __('Email:') }}</label>
    <input type="email" id="email" name="email" placeholder="{{ __('Enter your email') }}" required>

    <label for="phone">{{ __('Phone:') }}</label>
    <input type="tel" id="phone" name="phone" placeholder="{{ __('Enter your phone number') }}" pattern="^\+?(\d{1,3})?[-.\s]?\(?(\d{1,4})\)?[-.\s]?\d{1,4}[-.\s]?\d{1,4}[-.\s]?\d{1,4}$"
 required>

    <label for="date">{{ __('Preferred Demo Date:') }}</label>
    <input type="date" id="date" name="date" required>

    <label for="time">{{ __('Preferred Time:') }}</label>
    <input type="time" id="time" name="time" required>

    <label for="message">{{ __('Message (Optional):') }}</label>
    <textarea id="message" name="message" placeholder="{{ __('Any additional details or questions') }}"></textarea>

    <button type="submit" class="submit-btn">{{ __('Book Demo') }}</button>
</form>

<script>
  const form = document.getElementById('contactForm');
  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default browser behavior

    fetch(form.action, {
      method: "POST",
      headers: {
        'Accept': 'application/json'
      },
      body: new FormData(form)
    }).then(response => {
      if (response.ok) {
        alert("Your demo request has been sent!");
        setTimeout(() => {
    window.location.href = "https://app.grasshoppertech.co.za";
  }, 100);
        window.location.href = "https://app.grasshoppertech.co.za";
      } else {
        alert("Something went wrong. Please try again.");
      }
    }).catch(error => {
      console.error("Form submission error:", error);
      alert("Failed to send the message. Please check your connection and try again.");
    });
  });
</script>
</body>
</html>