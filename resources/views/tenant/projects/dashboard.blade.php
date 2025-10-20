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
            background-color: #ffffff;
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

        .project-table {
            width: 100%;
            background-color: #EBEAEE;
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            color: #000000;
        }

        .project-table th {
            background-color: #216592;
            color: #ffffff;
            padding: 10px;
            text-align: left;
        }

        .project-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }

        .project-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .project-table tr:hover {
            background-color: #e6e6e6;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .status-completed {
            background-color: #4b8da0;
            color: white;
        }

        .status-ongoing {
            background-color: #216592;
            color: white;
        }

        .status-planned {
            background-color: #34495e;
            color: white;
        }

        @media (min-width: 1200px) {
            .container {
                width: 1070px !important;
            }
        }
    </style>

    <div class="container">
        {{--        //UPDATED --}}
        <h1>Project Management Dashboard</h1>

        <form id="dateRangeForm" method="GET" action="#" style="margin-bottom: 20px;">
            <label for="start_date" style="color: #086177;">Start Date:</label>
            <input type="date" id="start_date" name="start_date" style="color: #086177;"
                value="{{ old('start_date', request('start_date')) }}" required>

            <label for="end_date" style="color: #086177;">End Date:</label>
            <input type="date" id="end_date" name="end_date" style="color: #086177;"
                value="{{ old('end_date', request('end_date')) }}" required>

            <button type="submit"
                style="background-color: #216592; color: #ffffff; border: none; padding: 5px 10px; border-radius: 4px;">Apply</button>
        </form>

        <div class="info-cards">
            <div class="info-card" style="background-color: #216592;">
                <div class="info-card-icon">📊</div>
                <div class="info-card-value">{{ $totalProjects ?? 0 }}</div>
                <div class="info-card-label">Total Projects</div>
            </div>
            <div class="info-card" style="background-color: #216592;">
                <div class="info-card-icon">🚀</div>
                <div class="info-card-value">{{ $activeProjects ?? 0 }}</div>
                <div class="info-card-label">Active Projects</div>
            </div>
            <div class="info-card" style="background-color: #34495e;">
                <div class="info-card-icon">🌴</div>
                <div class="info-card-value">{{ $plannedProjects ?? 0 }}</div>
                <div class="info-card-label">Total Vegetation Management Projects</div>
            </div>
            <div class="info-card" style="background-color: #4b8da0;">
                <div class="info-card-icon">✅</div>
                <div class="info-card-value">{{ $completedProjects ?? 0 }}</div>
                <div class="info-card-label">Completed Projects</div>
            </div>
        </div>

        <!-- First Row -->
        <div style="overflow-x: auto; width: 100%; margin-bottom: 20px;">
            <div style="display: flex; gap: 20px; min-width: max-content;">
                <div class="chart-container" style="flex: 0 0 600px; height: 350px;">
                    <canvas id="projectTypeDistribution"></canvas>
                </div>
                <div class="chart-container" style="flex: 0 0 600px; height: 350px;">
                    <canvas id="projectTimeline"></canvas>
                </div>
                <div class="chart-container" style="flex: 0 0 600px; height: 350px;">
                    <canvas id="projectLocationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div style="overflow-x: auto; width: 100%;">
            <div style="display: flex; gap: 20px; min-width: max-content;">
                <div class="chart-container" style="flex: 0 0 600px; height: 350px;">
                    <canvas id="projectCompletionRate"></canvas>
                </div>
                <div class="chart-container" style="flex: 0 0 600px; height: 350px;">
                    <canvas id="vehicleKmsChart"></canvas>
                </div>
                <div class="chart-container" style="flex: 0 0 600px; height: 350px;">
                    <canvas id="resourceAllocation"></canvas>
                </div>
            </div>
        </div>

        <h2 style="margin-top: 30px;">Recent Projects</h2>
        <div class="table-responsive">
            <table class="project-table">
                <thead>
                    <tr>
                        <th>Project Name</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Start Date</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Completion %</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($recentProjects as $project)
                        <tr>
                            <td>{{ $project->name }}</td>
                            <td>{{ $project->type }}</td>
                            <td>{{ $project->location }}</td>
                            <td>{{ $project->start_date->format('d M Y') }}</td>
                            <td>{{ $project->endDate->format('d M Y') }}</td>
                            <td>
                                @if ($project->status == 'completed')
                                    <span class="status-badge status-completed">Completed</span>
                                @elseif($project->status == 'in_progress')
                                    <span class="status-badge status-ongoing">Ongoing</span>
                                @else
                                    <span class="status-badge status-planned">Planned</span>
                                @endif
                            </td>
                            <td>{{ $project->completion_percentage }}%</td>
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

            // Project Type Distribution
            new Chart(document.getElementById('projectTypeDistribution').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Vegetation', 'Training', 'Innovation', 'Consulting', 'SMME Development',
                        'Others'
                    ],
                    datasets: [{
                        data: @json($projectTypeDistribution),
                        backgroundColor: ['#216592', '#4b8da0', '#34495e', '#0b3550']
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Project Type Distribution'
                        }
                    }
                }
            });

            // Project Timeline
            new Chart(document.getElementById('projectTimeline').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($timelineMonths),
                    datasets: [{
                            label: 'Planned Projects',
                            data: @json($plannedProjectsTimeline),
                            backgroundColor: '#34495e'
                        },
                        {
                            label: 'Ongoing Projects',
                            data: @json($ongoingProjectsTimeline),
                            backgroundColor: '#216592'
                        },
                        {
                            label: 'Completed Projects',
                            data: @json($completedProjectsTimeline),
                            backgroundColor: '#4b8da0'
                        }
                    ]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Project Timeline'
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });

            // Project Locations
            new Chart(document.getElementById('projectLocationChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($locationNames),
                    datasets: [{
                        label: 'Number of Projects',
                        data: @json($projectsByLocation),
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
                            text: 'Projects by Location'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: '#000000',
                                stepSize: 1
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

            // Project Completion Rate
            new Chart(document.getElementById('projectCompletionRate').getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($completionRateMonths),
                    datasets: [{
                        label: 'Completion Rate (%)',
                        data: @json($projectCompletionRate),
                        backgroundColor: 'rgba(75, 141, 160, 0.2)',
                        borderColor: '#4b8da0',
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
                            text: 'Project Completion Rate Trend'
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
                            max: 100,
                            ticks: {
                                color: '#000000'
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('vehicleKmsChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Actual', 'Vehicle Kms target'],
                    datasets: [{
                        data: @json([$vehicleActualKms ?? 0, $vehicleTargetKms ?? 0]),
                        backgroundColor: ['#4b8da0', '#216592']
                    }]
                },
                options: {
                    ...chartConfig,
                    plugins: {
                        ...chartConfig.plugins,
                        title: {
                            ...chartConfig.plugins.title,
                            display: true,
                            text: 'Vehicle Kms Distribution'
                        }
                    }
                }
            });

            const projectNames = {!! $projectNames !!};
            const projectBudgets = {!! $projectBudgets !!};
            const projectExpenses = {!! $projectExpenses !!};

            if (window.resourceChart) {
                window.resourceChart.destroy();
            }

            if (projectNames.length === 0) {
                window.resourceChart = new Chart(document.getElementById('resourceAllocation').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: projectNames,
                        datasets: [{
                                label: 'Project Budget',
                                data: projectBudgets,
                                borderColor: '#216592',
                                backgroundColor: 'rgba(33, 101, 146, 0.7)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Project Expenses',
                                data: projectExpenses,
                                borderColor: '#4b8da0',
                                backgroundColor: 'rgba(75, 141, 160, 0.7)',
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        ...chartConfig,
                        plugins: {
                            ...chartConfig.plugins,
                            title: {
                                ...chartConfig.plugins.title,
                                display: true,
                                text: 'Project Budgets vs Expenses'
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#000000'
                                },
                                stacked: false
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
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                    }
                });
            } else {
                window.resourceChart = new Chart(document.getElementById('resourceAllocation').getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: projectNames,
                        datasets: [{
                                label: 'Project Budget',
                                data: projectBudgets,
                                borderColor: '#216592',
                                backgroundColor: 'rgba(33, 101, 146, 0.7)',
                                borderWidth: 1,
                            },
                            {
                                label: 'Project Expenses',
                                data: projectExpenses,
                                borderColor: '#4b8da0',
                                backgroundColor: 'rgba(75, 141, 160, 0.7)',
                                borderWidth: 1,
                            }
                        ]
                    },
                    options: {
                        ...chartConfig,
                        plugins: {
                            ...chartConfig.plugins,
                            title: {
                                ...chartConfig.plugins.title,
                                display: true,
                                text: 'Project Budgets vs Expenses'
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: '#000000'
                                },
                                stacked: false
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
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                    }
                });
            }

        });
    </script>
@endsection
