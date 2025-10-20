@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #000000;
            color: #ffffff;
        }
        .dashboard-container {
            background-color: #EBEAEE;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-cards {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-card {
            background-color: #216592;
            border-radius: 8px;
            padding: 15px;
            width: 23%;
            text-align: center;
            color: #ffffff;
            transition: transform 0.3s ease;
        }
        .info-card:hover {
            transform: scale(1.05);
        }
        .info-card-icon {
            font-size: 24px;
            margin-bottom: 10px;
            color: #ffffff;
        }
        .info-card-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info-card-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .chart-container {
            background-color: #EBEAEE;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            height: 400px;
        }
        .smme-table {
            width: 100%;
            background-color: #EBEAEE;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            color: #000000;
        }
        .smme-table th {
            background-color: #216592;
            color: #ffffff;
            padding: 10px;
            text-align: left;
        }
        .smme-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .smme-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .smme-table tr:hover {
            background-color: #e6e6e6;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-overdue { background-color: #086177; color: white; }
        .status-pending { background-color: #427998; color: white; }
        .status-paid { background-color: #2a89a6; color: white; }
        h1, h2 {
            color: #0f74a8;
        }
    </style>

    <div class="container">
        <h1 class="mb-4">SMME Analytics Dashboard</h1>

        <div class="info-cards">
            <div class="info-card">
                <div class="info-card-icon">🏢</div>
                <div class="info-card-value">{{ $totalSmmes }}</div>
                <div class="info-card-label">Total SMMEs</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">✅</div>
                <div class="info-card-value">{{ $verifiedSmmes }}</div>
                <div class="info-card-label">Verified SMMEs</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">📊</div>
                <div class="info-card-value">{{ $averageExperience }}</div>
                <div class="info-card-label">Avg Years Experience</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">📅</div>
                <div class="info-card-value">{{ $lastRegisteredSmme->name ?? 'N/A' }}</div>
                <div class="info-card-label">Latest Registered SMME</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="chart-container">
                <canvas id="smmeStatusChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="smmeGradeChart"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="smmeRegistrationTrendChart"></canvas>
            </div>
        </div>

        <h2 class="mt-4 mb-3">Recently Registered SMMEs</h2>
        <div class="table-responsive">
            <table class="smme-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Registration Number</th>
                    <th>Years of Experience</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @foreach($recentSmmes as $smme)
                    <tr>
                        <td>{{ $smme->name }}</td>
                        <td>{{ $smme->registration_number }}</td>
                        <td>{{ $smme->years_of_experience }}</td>
                        <td>{{ $smme->grade }}</td>
                        <td>
                                <span class="status-badge status-{{ strtolower($smme->status) }}">
                                    {{ $smme->status }}
                                </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartConfig = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#333'
                        }
                    },
                    title: {
                        color: '#333',
                        font: {
                            size: 16
                        }
                    }
                }
            };

            // SMME Status Chart
            new Chart(document.getElementById('smmeStatusChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Green', 'Yellow', 'Red'],
                    datasets: [{
                        data: @json($smmeStatusData),
                        backgroundColor: ['#2a89a6', '#427998', '#086177']
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'SMME Status Distribution'
                        }
                    }
                }
            });

            // SMME Grade Chart
            new Chart(document.getElementById('smmeGradeChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json(array_keys($smmeGradeData)),
                    datasets: [{
                        label: 'Number of SMMEs',
                        data: @json(array_values($smmeGradeData)),
                        backgroundColor: '#3498db'
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'SMME Grade Distribution'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // SMME Registration Trend Chart
            new Chart(document.getElementById('smmeRegistrationTrendChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json(array_keys($smmeRegistrationTrendData)),
                    datasets: [{
                        label: 'SMME Registrations',
                        data: @json(array_values($smmeRegistrationTrendData)),
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderColor: '#3498db',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Monthly SMME Registration Trend'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection