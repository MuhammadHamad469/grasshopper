<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>The Grasshopper</title>
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
        }

        body {
            font-family: Tahoma, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: var(--primary-color);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-banner {
            background: var(--bg-gradient);
            padding: 0.5rem 0;
            margin-top: 40px;
        }

        .centered-logo {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .centered-logo img {
            max-width: 300px;
            height: auto;
            margin-top: 40px;
            margin-bottom: -173px;
        }

        .nav-links {
            display: flex;
            list-style: none;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-size: 18px;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            width: 0;
            height: 3px;
            background: var(--secondary-color);
            position: absolute;
            left: 0;
            bottom: -6px;
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background-color: var(--secondary-color);
            color: var(--text-color);
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn:hover {
            background-color: #34495e;
            color: #fff;
        }

        .hero {
            background: var(--bg-gradient);
            color: #fff;
            padding: 50px 0 30px;
            min-height: 80vh;
            display: flex;
            align-items: center;
            margin-top: -5rem;
        }

        .hero .container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
        }

        .hero-content {
            flex: 4;
            max-width: 80%;
            text-align: center;
            margin: 0 auto;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }


        .features {
            background-color: #f8f9fa;
            padding: 5rem 0;
        }

        .feature-cards {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .feature-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .feature-card p {
            color: #666;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .feature-link {
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }

        .feature-link:hover {
            color: var(--primary-color);
        }

        .feature-link i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .feature-link:hover i {
            transform: translateX(5px);
        }

        .card {
            background-color: #fff;
            padding: 2rem;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 250px;
            max-width: 350px;
        }

        .card h3 {
            margin-bottom: 1rem;
        }

        .card p {
            color: #666;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        #plan {
            padding: 4rem 0;
        }

        .plans-list {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 50px;
        }

        .plan-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .plan-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .plan-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .plan-header h2 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .price {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .price .currency {
            font-size: 1.5rem;
            vertical-align: super;
        }

        .price .period {
            font-size: 1rem;
            color: #666;
        }

        .features-list {
            list-style-type: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .features-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .features-list i {
            color: #4CAF50;
            margin-right: 0.5rem;
        }

        .plan-description {
            text-align: center;
            margin-bottom: 1.5rem;
            flex-grow: 1;
            font-weight: bold;
        }

        .plan-card .btn {
            width: 100%;
            text-align: center;
        }

        .plan-card.premium {
            transform: scale(1.05);
            border: 2px solid var(--secondary-color);
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 2rem 0;
        }

        .social-links {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .social-links a {
            color: #fff;
            font-size: 24px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: var(--secondary-color);
        }

        .copyright {
            font-size: 14px;
            color: #bbb;
        }

        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-content,
            .hero-image {
                max-width: 100%;
            }

            .hero-image {
                order: -1;
            }

            .nav-links {
                display: none;
            }

            .feature-cards,
            .plans-list {
                flex-direction: column;
                align-items: center;
            }

            .card,
            .plans-list div {
                width: 100%;
                max-width: none;
            }

            footer .container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .policy-links ul {
                display: flex;
                gap: 20px;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .social-links ul {
                display: flex;
                gap: 15px;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .copyright {
                margin: 0;
                text-align: center;
                margin-top: 20px;
            }
        }
    </style>
    <style>
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .custom-modal-content {
            position: relative;
            margin: 5% auto;
            padding: 0;
            width: 80%;
            max-width: 900px;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }

        .custom-modal video {
            width: 100%;
            height: auto;
            display: block;
        }

        .custom-close {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10;
        }

        .custom-close:hover {
            color: #f00;
        }
    </style>
    <style>
        .hero-section {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .hero-bg-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -2;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .hero-content-wrapper {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 0 20px;
            max-width: 900px;
        }

        .hero-content h1 {
            font-size: 3em;
            margin-bottom: 20px;
            color: black;
        }

        .hero-content p {
            color: black;
            font-size: 1.2em;
            line-height: 1.6em;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <nav>

                <div></div>
                <ul class="nav-links">
                    <li><a href="#features">Features</a></li>
                    <li><a href="#plan">Pricing</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('book-demo') }}" class="btn">Book A Demo</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="logo-banner">
        <div class="container">
            <div class="centered-logo">
                <img src="{{ asset('/img/grasshopper-logo.png') }}" alt="Grasshopper logo">
            </div>
        </div>
    </section>

    <section id="home" class="hero-section">
        <video autoplay muted loop playsinline class="hero-bg-video">
            <source src="{{ asset('/home_video/home.mp4') }}" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
        <div class="hero-overlay"></div>
        <div class="hero-content-wrapper">
            <div class="hero-content">
                <h1>The Grasshopper</h1>
                <p>
                    The Grasshopper is a web and mobile-based project management technology developed and customizable
                    to enhance organisational performance. This all-in-one platform integrates project organization,
                    advanced analytics, financial management tools, and resource optimization features. With an
                    interactive dashboard offering real-time insights, seamless quoting and invoicing, and environmental
                    impact tracking, The Grasshopper aims to streamline operations, improve performance, and foster
                    growth while prioritizing environmental stewardship.
                </p>
            </div>
        </div>
    </section>
    <section id="features" class="features">
        <div class="container">
            <h2 class="section-header">Our Features</h2>
            <div class="feature-cards">
                @foreach (['Project Management', 'Asset Management', 'Supplier Management'] as $feature)
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i
                                class="fa {{ $feature === 'Project Management' ? 'fa-tasks' : ($feature === 'Asset Management' ? 'fa-line-chart' : 'fa-building') }}"></i>
                        </div>
                        <h3>{{ $feature }}</h3>
                        <p>
                            @switch($feature)
                                @case('Project Management')
                                    Organize, plan, and oversee business projects efficiently. Ensure timely completion within
                                    budget constraints.
                                @break

                                @case('Asset Management')
                                    Track, manage, and optimize company assets. Critical for SMMEs to enhance business
                                    performance and compliance.
                                @break

                                @case('Supplier Management')
                                    Manage and evaluate supplier performance while tracking expenditure and maintaining
                                    procurement relationships.
                                @break
                            @endswitch
                        </p>
                        <a href="javascript:void(0);" class="feature-link" onclick="openVideoModal()">Learn More <i
                                class="fa fa-arrow-right"></i></a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <div id="videoModal" class="custom-modal">
        <div class="custom-modal-content">
            <span class="custom-close" onclick="closeVideoModal()">&times;</span>
            <video id="featureVideo" controls>
                <source src="{{ asset('/public/home_video/home.mp4') }}" type="video/mp4">
                Your browser does not support HTML5 video.
            </video>
        </div>
    </div>
    <section id="plan" class="pricing">
        <div class="container">
            <h2 class="section-header">Our Plans</h2>
            <div class="plans-list">
                @foreach (['Basic', 'Premium', 'Standard'] as $plan)
                    <div class="plan-card {{ strtolower($plan) }}">
                        <div class="plan-header">
                            <i
                                class="fas fa-{{ $plan === 'Basic' ? 'leaf' : ($plan === 'Standard' ? 'tree' : 'award') }}"></i>
                            <h2>{{ $plan }}</h2>
                            <div class="price">
                                <span class="currency">R</span>
                                <span
                                    class="amount">{{ $plan === 'Basic' ? '800' : ($plan === 'Standard' ? '1400' : 'Custom') }}</span>
                                @if ($plan !== 'Premium')
                                    <span class="period">/month</span>
                                @endif
                            </div>
                        </div>
                        <ul class="features-list">
                            @if ($plan === 'Basic')
                                <li><i class="fas fa-check"></i> 10 Users</li>
                                <li><i class="fas fa-check"></i> 50 Projects</li>
                                <li><i class="fas fa-check"></i> Dashboard Insights</li>
                                <li><i class="fas fa-check"></i> Project Tracking</li>
                                <li><i class="fas fa-check"></i> Finance Management</li>
                                <li><i class="fas fa-check"></i> Resource Allocation</li>
                                <li><i class="fas fa-check"></i> Basic Support</li>
                            @elseif($plan === 'Premium')
                                <li><i class="fas fa-check"></i> All Standard Features</li>
                                <li><i class="fas fa-check"></i> Unlimited Users</li>
                                <li><i class="fas fa-check"></i> Unlimited Projects</li>
                                <li><i class="fas fa-check"></i> Custom Support</li>
                                <li><i class="fas fa-check"></i> Onboarding Assistance</li>
                                <li><i class="fas fa-check"></i> Report Analysis</li>
                                <li><i class="fas fa-check"></i> Performance Optimization</li>
                                <li><i class="fas fa-check"></i> Tailored Features</li>
                                <li><i class="fas fa-check"></i> Dedicated Account Manager</li>
                            @else
                                <li><i class="fas fa-check"></i> All Basic Features</li>
                                <li><i class="fas fa-check"></i> 20 Users</li>
                                <li><i class="fas fa-check"></i> 100 Projects</li>
                                <li><i class="fas fa-check"></i> Priority Support</li>
                                <li><i class="fas fa-check"></i> Live Training</li>
                                <li><i class="fas fa-check"></i> Limited Custom Changes</li>
                                <li><i class="fas fa-check"></i> Reports On Request</li>
                            @endif
                        </ul>
                        <p class="plan-description">
                            @if ($plan === 'Basic')
                                Essential features to get started and grow.
                            @elseif($plan === 'Standard')
                                Optimise operations across your entire organisation.
                            @else
                                Tailored features to elevate your organization's performance.
                            @endif
                        </p>
                        <a href="{{ route('book-demo') }}" class="btn">Get Started</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <ul class="social-links">
                @foreach (['linkedin', 'facebook', 'instagram'] as $social)
                    <li><a href="https://www.{{ $social }}.com" target="_blank"
                            aria-label="{{ ucfirst($social) }}"><i class="fab fa-{{ $social }}"></i></a></li>
                @endforeach
            </ul>

            <p class="copyright">
                &copy; {{ date('Y') }} Grasshopper Green Technologies. All Rights Reserved. |
                <a href="{{ asset('policies/PRIVACY_POLICY.pdf') }}" target="_blank" rel="noopener noreferrer">Privacy
                    Policy</a> |
                <a href="{{ asset('policies/Grasshopper_Green_Technology_Website.pdf') }}" target="_blank"
                    rel="noopener noreferrer">Terms and Conditions</a> |
                <a href="{{ asset('policies/Cookies_Policy.pdf') }}" target="_blank"
                    rel="noopener noreferrer">Cookies
                    Policy</a> |
                <a href="{{ asset('policies/Data_Retention_Policy.pdf') }}" target="_blank"
                    rel="noopener noreferrer">Data Retention</a> |
                <a href="{{ asset('policies/Promotion_of_Access_to_Information.pdf') }}" target="_blank"
                    rel="noopener noreferrer">PAIA</a>
            </p>
        </div>
    </footer>

    <style>
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }

        .copyright {
            text-align: center;
            margin: 0;
        }

        .copyright a {
            color: inherit;
            text-decoration: none;
        }

        .copyright a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        var modal = document.getElementById('videoModal');
        var video = document.getElementById('featureVideo');

        function openVideoModal() {
            modal.style.display = 'block';
            video.play();
        }

        function closeVideoModal() {
            video.pause();
            video.currentTime = 0;
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeVideoModal();
            }
        }
    </script>
</body>

</html>
