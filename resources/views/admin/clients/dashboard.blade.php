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
        h1,h2 {
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
        #activeInactiveClients {
              width:350px !important;
              height:250px !important;
        }
        #modulesUsage{
          width:500px !important;
           height: 250px !important;         
        }
        #projectCompletionRate{
              width:500px !important;
              height:270px !important;
        }
    </style>

    <div class="container">
        <h1>Client Dashboard</h1>
        <!-- <form id="dateRangeForm" method="GET" action="#" style="margin-bottom: 20px;">
            <label for="start_date" style="color: #086177;">Start Date:</label>
            <input type="date" id="start_date" name="start_date" style="color: #086177;" value="{{ request('start_date') }}" required>
            <label for="end_date" style="color: #086177;">End Date:</label>
            <input type="date" id="end_date" name="end_date" style="color: #086177;" value="{{ request('end_date') }}" required>
            <button type="submit" style="background-color: #216592; color: #ffffff; border: none; padding: 5px 10px; border-radius: 4px;">Apply</button>
        </form> -->

        <div class="info-cards">
            <div class="info-card" style="background-color: #216592;">
                <div class="info-card-icon">📊</div>
                <div class="info-card-value">{{$totalClients ?? 0}}</div>
                <div class="info-card-label">Total Users</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); 
            grid-template-rows: repeat(1, 1fr); 
            gap: 20px;">
            <div class="chart-container" style="width: 100%; height: 300px;">
                <div class="mb-3">
                    <select id="modulesUsageClientsFilter" class="form-control" style="width: 100px;" data-client-id="{{ $client->id}}">
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                <canvas id="modulesUsage"></canvas>
            </div>

            <div class="chart-container" style="width: 100%; height: 300px;">
                <canvas id="projectCompletionRate"></canvas>
            </div>
        </div>

        <!-- Total Users -->
        <h2 style="margin-top: 30px;">Total Users</h2>
        <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped datatable total-users-table">
                <thead>
                    <tr>
                        <th>Sno</th>
                        <th>@lang('quickadmin.users.fields.name')</th>
                        <th>@lang('quickadmin.users.fields.email')</th>
                    </tr>
                </thead>
                
                <tbody style="color: black !important;">
                    @php
                        $sno = 1;
                    @endphp
                    @if (count($totalUsers) > 0)
                        @foreach ($totalUsers as $key =>$user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td style="color: black !important;">{{ $sno++ }}</td>
                                <td field-key='name' style="color: black !important;">{{ $user->name }}</td>
                                <td field-key='email' style="color: black !important;">{{ $user->email  }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="container" style="padding: 0">
        <!-- User Sessions -->
        <h2 style="margin-top: 30px;">User Sessions</h2>
        <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <!-- <table class="table table-bordered table-striped {{ count($totalUserAverageSessionDuration) > 0 ? 'datatable' : '' }}"> -->
            <table class="table table-bordered table-striped datatable user-sessions-table">

                <thead>
                <div class="mb-2">
                    <select id="userSessionsClientsFilter" class="form-control" style="width: 100px;"data-client-id="{{ $client->id}}">
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
                    <tr>
                        <th>Sno</th>
                        <th>Name</th>
                        <th>Average Session Time (hr)</th>
                        <th>Average Logins</th>
                        <th>Total Logins</th>
                    </tr>
                </thead>
                
                <tbody style="color: black !important;">
                    @php
                        $sno = 1;
                    @endphp
                    @if (count($totalUserAverageSessionDuration) > 0)
                        @foreach ($totalUserAverageSessionDuration as $list)
                            <tr data-entry-id="{{ $user->id }}">
                                <td style="color: black !important;">{{ $sno++ }}</td>
                                @php
                                    $averageSessionTime = round($list->session_time,2);
                                    $averageLoginCount  = round($list->user_login_count,2);
                                @endphp
                                <td field-key='session_time' style="color: black !important;">{{ $list->user_name }}</td>
                                <td field-key='session_time' style="color: black !important;">{{ $averageSessionTime }}</td>
                                <td field-key='user_login_count' style="color: black !important;">{{ $averageLoginCount  }}</td>
                                <td field-key='session_time' style="color: black !important;">{{ $list->user_login_count }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.total-users-table').DataTable( {
                pageLength: 10,
            } );

            userSessionsClientsFilterDT = $('.user-sessions-table').DataTable({
                pageLength: 10, // default rows
                destroy: true // allows reinitialization
            });

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

            // Initialize chart variables
            let modulesUsageChart   =  null;
            // let projectTimeline     =  null;
            let projectCompletionRate =  null;



            // Initialize the first chart
            initializeModulesUsageChart();
            // initializeBarChart();
            initializeRateChart();

            // Modules Usage Chart (static for this example)
            function initializeModulesUsageChart() {
                const ctx = document.getElementById('modulesUsage').getContext('2d');
                modulesUsageChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: @json($modulesUsageLabels),
                        datasets: [{
                            data: @json($modulesUsageCounts),
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
                                text: 'Modules Usage Per Hour'
                            }
                        }
                    }
                });
            }

            // Project Timeline
            // function initializeBarChart(){
            //      const ctx = document.getElementById('projectTimeline').getContext('2d');
            //      projectTimeline =  new Chart(ctx, {
            //         type: 'bar',
            //         data: {
            //             labels: @json($months),
            //             datasets: [
            //                 {
            //                     label: 'Total Users',
            //                     data: @json($total_users_count),
            //                     backgroundColor: '#34495e'
            //                 },
            //             ]
            //         },
            //         options: {
            //             ...chartConfig,
            //             plugins: {
            //                 ...chartConfig.plugins,
            //                 title: {
            //                     ...chartConfig.plugins.title,
            //                     display: true,
            //                     text: 'Users Timeline'
            //                 }
            //             }
            //         }    
            //     });
            // }
            // Project Completion Rate
            function initializeRateChart(argument) {
                const ctx = document.getElementById('projectCompletionRate').getContext('2d');
                    projectCompletionRate =             // Project Completion Rate
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($months),
                            datasets: [{
                                label: 'Users Created Number',
                                data: @json($total_users_count),
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
                                    text: 'Users Created Per Month',
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
            }

            // Filter change event active Inactive Clients Filter
            $(document).on('change', '#modulesUsageClientsFilter', function() {
                const filterType = $(this).val();
                const client_id = $(this).data('client-id');
                initializeNewModulesUsageChart(filterType, client_id);
            });

            // Filter change event user Sessions Clients Filter
            $(document).on('change', '#userSessionsClientsFilter', function() {
                const filterType = $(this).val();
                const client_id = $(this).data('client-id');
                fetchUserSessions(filterType, client_id);
            });

            function initializeNewModulesUsageChart(filterType, client_id) {
                let url = `{{ route('admin.clients.module-usage-data') }}`;
                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: { 
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: {
                        filter: filterType,
                        client_id: client_id
                    },
                    success: function(response) {
                        if (response.success) {
                            // Destroy previous chart if it exists
                            if (modulesUsageChart) {
                                modulesUsageChart.destroy();
                            }

                            // Create new chart
                            const ctx = document.getElementById('modulesUsage').getContext('2d');
                            modulesUsageChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: response.data.modulesUsageLabels,
                                    datasets: [{
                                        data: response.data.modulesUsageCounts,
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
                                            text: 'Module Usage chart'
                                        }
                                    }
                                }
                            });
                        } else {
                            console.error('Error loading chart data');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            }

            // User Sessions Clients Chart 
            function fetchUserSessions(filterType, client_id) {
                let url = `{{ route('admin.clients.user-sessions-data') }}`;

                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: { 
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: {
                        filter: filterType,
                        client_id: client_id
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data.totalUserAverageSessionDuration;
                            const division = response.division;


                            // Clear old data
                            userSessionsClientsFilterDT.clear();

                            // Append new data
                            let sno = 1;
                            data.forEach(item => {
                                console.log(item);
                                const averageSessionTime = (item.session_time/division) ? item.session_time.toFixed(2) : 0;
                                const averageLoginCount  = (item.user_login_count / division).toFixed(2);

                                userSessionsClientsFilterDT.row.add([
                                    sno++,
                                    item.user_name,
                                    averageSessionTime ,
                                    averageLoginCount,
                                    item.user_login_count
                                ]);
                            });

                            // Redraw DataTable
                            userSessionsClientsFilterDT.draw();
                        } else {
                            console.error('Error loading chart data');
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr.responseText);
                    }
                });
            }
        });
    </script>
@endsection