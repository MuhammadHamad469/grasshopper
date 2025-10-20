@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.clients.title')</h3>
    @can('client_create')
    @endcan
    <p>
        <a href="{{ route('admin.clients.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
    </p>

    <div style="height: 308px;">
        <div class="chart-container" style="width: 30%; height: 261px;">
            <div class="mb-3">
                <select id="activeInactiveClientsFilter" class="form-control" style="width: 130px;">
                    <option value="monthly">Monthly</option>
                    <option value="three_month" selected>3 Months</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <canvas id="activeInactiveClients"></canvas>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($clients) > 0 ? 'datatable' : '' }} @can('client_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('role_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan

                        <th>@lang('quickadmin.clients.fields.name')</th>
                        <th>@lang('quickadmin.clients.fields.db_host')</th>
                        <th>@lang('quickadmin.clients.fields.db_port')</th>
                        <th>@lang('quickadmin.clients.fields.db_name')</th>
                        <th>@lang('quickadmin.clients.fields.db_username')</th>
                        <th>@lang('quickadmin.clients.fields.is_active')</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($clients) > 0)
                        @foreach ($clients as $client)
                            <tr data-entry-id="{{ $client->id }}">
                                @can('client_delete')
                                    <td></td>
                                @endcan

                                <td field-key='name'>{{ $client->name }}</td>
                                <td field-key='db_host'>{{ $client->db_host }}</td>
                                <td field-key='db_port'>{{ $client->db_port }}</td>
                                <td field-key='db_name'>{{ $client->db_name }}</td>
                                <td field-key='db_username'>{{ $client->db_username }}</td>
                                <td field-key='is_active'>{{ $client->is_active == '1' ? 'Active' : 'Inactive' }}</td>
                                <td>
                                    <!-- @can('client_view')
                                    <a href="{{ route('admin.clients.show',[$client->id]) }}" class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan -->
                                    <a href="{{ route('admin.clients.dashboard',[$client->id]) }}" class="btn btn-xs btn-success">@lang('quickadmin.qa_dashboard')</a>
                                    @can('client_edit')
                                    <a href="{{ route('admin.clients.edit',[$client->id]) }}" class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('client_delete')
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('".trans("quickadmin.qa_are_you_sure")."');",
                                        'route' => ['admin.clients.destroy', $client->id])) !!}
                                    {!! Form::submit(trans('quickadmin.qa_delete'), array('class' => 'btn btn-xs btn-danger')) !!}
                                    {!! Form::close() !!}
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activeInactiveChart = null;
            initializeActiveInactiveChart('three_month');

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

            // Active and Inactive Clients Chart
            function initializeActiveInactiveChart(filterType) {
                let url = `{{ route('admin.clients.active-inactive-data') }}`;
                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: { 
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    data: {
                        filter: filterType
                    },
                    success: function(response) {
                        if (response.success) {
                            // Destroy previous chart if it exists
                            if (activeInactiveChart) {
                                activeInactiveChart.destroy();
                            }

                            // Create new chart
                            const ctx = document.getElementById('activeInactiveClients').getContext('2d');
                            activeInactiveChart = new Chart(ctx, {
                                type: 'pie',
                                data: {
                                    labels: response.data.labels,
                                    datasets: [{
                                        data: response.data.values,
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
                                            text: 'Active vs Inactive Clients'
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

            // Filter change event active Inactive Clients Filter
            $(document).on('change', '#activeInactiveClientsFilter', function() {
                const filterType = $(this).val();
                initializeActiveInactiveChart(filterType);
            });
        });
    </script>
@stop