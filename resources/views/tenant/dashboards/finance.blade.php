@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #000000;
            color: #ffffff;
        }

        .chart-container {
            background-color: #EBEAEE;
            border-radius: 8px;
            padding: 15px;
        }

        h1,
        h2 {
            color: #0f74a8;
        }

        p {
            color: #ffffff;
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
        }

        .info-card-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .info-card-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .info-card-label {
            font-size: 14px;
        }

        .finance-table {
            width: 100%;
            background-color: #EBEAEE;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            color: #000000;
        }

        .finance-table th {
            background-color: #216592;
            color: #ffffff;
            padding: 10px;
            text-align: left;
        }

        .finance-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        .finance-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .finance-table tr:hover {
            background-color: #e6e6e6;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .status-overdue {
            background-color: #086177;
            color: white;
        }

        .status-pending {
            background-color: #427998;
            color: white;
        }

        .status-paid {
            background-color: #2a89a6;
            color: white;
        }
    </style>

    <div class="container">
        <h1>Financial Analytics Dashboard</h1>
        <div class="info-cards">
            <div class="info-card">
                <div class="info-card-icon">💰</div>
                <div class="info-card-value">{{ $totalInvoiceAmount }}</div>
                <div class="info-card-label">Total Invoiced Amount</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">📝</div>
                <div class="info-card-value">{{ $totalQuotedAmount }}</div>
                <div class="info-card-label">Total Quoted Amount</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">⏰</div>
                <div class="info-card-value">{{ $overdueInvoicesCount }}</div>
                <div class="info-card-label">Overdue Invoices</div>
            </div>
            <div class="info-card">
                <div class="info-card-icon">📊</div>
                <div class="info-card-value">{{ $conversionRate }}%</div>
                <div class="info-card-label">Quote to Invoice Conversion</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr); gap: 20px;">
            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="quoteStatusChart"></canvas>
            </div>

            <!-- 👇 Only this one modified -->
            <div class="chart-container" style="width: 100%; height: 300px; overflow-x: auto; white-space: nowrap;">
                <canvas id="topClientsChart" style="min-width: 500px;"></canvas>
            </div>


            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="revenueComparisonChart"></canvas>
            </div>
            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="invoiceAgeingChart"></canvas>
            </div>
            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>

        <h2 style="margin-top: 30px;">Recent Invoices</h2>
        <div class="table-responsive">
            <table class="finance-table">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Client Name</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentInvoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->client_name }}</td>
                            <td>{{ $invoice->issue_date->format('d M Y') }}</td>
                            <td>{{ $invoice->expiry_date->format('d M Y') }}</td>
                            <td>{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                @if ($invoice->expiry_date < now())
                                    <span class="status-badge status-overdue">Overdue</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
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
                            color: '#000000'
                        }
                    },
                    title: {
                        color: '#000000',
                        font: {
                            size: 16
                        }
                    }
                }
            };

            // Monthly Revenue Chart
            const monthlyRevenueData = @json($monthlyRevenueData);
            const monthlyRevenueLabels = @json($monthlyRevenueLabels);
            new Chart(document.getElementById('monthlyRevenueChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($monthlyRevenueLabels),
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: Object.values(monthlyRevenueData),
                        backgroundColor: 'rgba(33, 101, 146, 0.2)',
                        borderColor: '#216592',
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
                            text: 'Monthly Revenue Trend'
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#000000'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000',
                                callback: function(value) {
                                    return 'R' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Quote Status Chart
            new Chart(document.getElementById('quoteStatusChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Converted', 'Expired', 'Pending'],
                    datasets: [{
                        data: @json($quoteStatusData),
                        backgroundColor: ['#086177', '#427998', '#2a89a6']
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Quote Conversion Status'
                        }
                    }
                }
            });

            // Top Clients Chart
            new Chart(document.getElementById('topClientsChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($topClientsLabels),
                    datasets: [{
                        label: 'Total Invoiced Amount',
                        data: @json($topClientsData),
                        backgroundColor: '#216592'
                    }]
                },
                options: {
                    ...chartConfig,
                    indexAxis: 'y',
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Top Clients by Invoice Amount'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000',
                                callback: function(value) {
                                    return 'R' + value.toLocaleString();
                                }
                            }
                        },
                        y: {
                            ticks: {
                                color: '#000000'
                            }
                        }
                    }
                }
            });

            // Revenue Comparison Chart
            new Chart(document.getElementById('revenueComparisonChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Invoiced', 'Quoted'],
                    datasets: [{
                        label: 'Total Amount',
                        data: @json($revenueComparisonData),
                        backgroundColor: ['#4b8da0', '#34495e']
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Invoiced vs Quoted Revenue'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000',
                                callback: function(value) {
                                    return 'R' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            
            // Invoice Ageing Chart
            new Chart(document.getElementById('invoiceAgeingChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['0-30 Days', '31-60 Days', '61-90 Days', '90+ Days'],
                    datasets: [{
                        data: @json($invoiceAgeingData),
                        backgroundColor: ['#2a89a6', '#427998', '#086177', 'rgb(14,51,62)']
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Invoice Ageing Analysis'
                        }
                    }
                }
            });

            // Payment Method Chart
            new Chart(document.getElementById('paymentMethodChart').getContext('2d'), {
                type: 'radar',
                data: {
                    labels: ['Bank Transfer', 'Credit Card', 'PayPal', 'Stripe', 'Other'],
                    datasets: [{
                        label: 'Payment Method Distribution',
                        data: @json($paymentMethodData),
                        backgroundColor: 'rgba(33, 101, 146, 0.2)',
                        borderColor: '#216592',
                        pointBackgroundColor: '#216592'
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Payment Method Distribution'
                        }
                    },
                    scales: {
                        r: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000',
                                stepSize: 20
                            },
                            pointLabels: {
                                color: '#000000'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
