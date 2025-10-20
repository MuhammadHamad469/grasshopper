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
    h1, p {
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
</style>

<div class="container">
    <h1>Project Management Dashboard</h1>

    <div class="info-cards">
        <div class="info-card" style="background-color: #216592;">
            <div class="info-card-icon">📊</div>
            <div class="info-card-value">{{$activeProjects ?? 66}}</div>
            <div class="info-card-label">Active Projects</div>
        </div>
        <div class="info-card" style="background-color: #4b8da0;">
            <div class="info-card-icon">✅</div>
            <div class="info-card-value">{{$completedProjects ?? 234}}</div>
            <div class="info-card-label">Completed Projects</div>
        </div>
        <div class="info-card" style="background-color: #34495e;">
            <div class="info-card-icon">⏱️</div>
            <div class="info-card-value">{{$upcomingDeadlines ?? 234}}</div>
            <div class="info-card-label">Upcoming Deadlines</div>
        </div>
        <div class="info-card" style="background-color: #4f42b4;">
            <div class="info-card-icon">👥</div>
            <div class="info-card-value">{{$totalTeams ?? 42}}</div>
            <div class="info-card-label">Teams Assigned</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); grid-template-rows: repeat(2, 1fr); gap: 20px;">
        <div class="chart-container" style="width: 100%; height: 300px;">
            <canvas id="projectTypeDistribution"></canvas>
        </div>
        <div class="chart-container" style="width: 100%; height: 300px;">
            <canvas id="projectTimeline"></canvas>
        </div>
        <div class="chart-container" style="width: 100%; height: 300px;">
            <canvas id="projectLocationChart"></canvas>
        </div>
        <div class="chart-container" style="width: 100%; height: 300px;">
            <canvas id="projectCompletionRate"></canvas>
        </div>
        <div class="chart-container" style="width: 100%; height: 300px;">
            <canvas id="teamPerformance"></canvas>
        </div>
        <div class="chart-container" style="width: 100%; height: 300px;">
            <canvas id="resourceAllocation"></canvas>
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

            @foreach($recentProjects as $project)
            <tr>
                <td>{{ $project->name }}</td>
                <td>{{ $project->type }}</td>
                <td>{{ $project->location }}</td>
                <td>{{ $project->start_date->format('d M Y') }}</td>
                <td>{{ $project->endDate->format('d M Y') }}</td>
                <td>
                    @if($project->status == 'completed')
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
                labels: ['Vegetation', 'Training', 'Innovation', 'Planning', 'Other'],
                datasets: [{
                    data: @json($projectTypeDistribution),
                    backgroundColor: ['#216592', '#4b8da0', '#34495e', '#4f42b4', '#7f8c8d']
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
                datasets: [
                    {
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
                    x: { ticks: { color: '#000000' } },
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
                    y: { ticks: { color: '#000000' } }
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
                    x: { ticks: { color: '#000000' } },
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

        // Team Performance
        new Chart(document.getElementById('teamPerformance').getContext('2d'), {
            type: 'radar',
            data: {
                labels: @json($teamNames),
                datasets: [{
                    label: 'On-time Completion Rate',
                    data: @json($teamPerformance),
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
                        text: 'Team Performance'
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
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

        // Resource Allocation
        new Chart(document.getElementById('resourceAllocation').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Staff', 'Equipment', 'Materials', 'Transport', 'Other'],
                datasets: [{
                    data: @json($resourceAllocation),
                    backgroundColor: ['#216592', '#4b8da0', '#34495e', '#4f42b4', '#7f8c8d']
                }]
            },
            options: {
                ...chartConfig,
                plugins: {
                    ...chartConfig.plugins,
                    title: {
                        ...chartConfig.plugins.title,
                        display: true,
                        text: 'Resource Allocation'
                    }
                }
            }
        });
    });
</script>
@endsection