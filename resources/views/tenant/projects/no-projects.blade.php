<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #216592; /* Custom background color */
        }
        .btn-custom {
            background-color: #34495e; /* Custom button background */
            color: #fff; /* White text color */
            border: none; /* Remove border */
        }
        .btn-custom:hover {
            background-color: #2c3e50; /* Darker shade on hover */
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
<div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
    <div class="card-body text-center">
        <h1 class="text-danger fw-bold mb-3">Access Error</h1>
        <p class="text-secondary">You don't have permission to view any project type. Contact your manager/admin.</p>
        <a href="{{ url()->previous() }}" class="btn btn-custom mt-3">Go Back</a>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>