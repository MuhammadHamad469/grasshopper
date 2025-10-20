@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.teams.title')</h3>
    @can('team_create')
        <p>
            <a href="{{ route('admin.teams.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>
        </p>
    @endcan

    {{-- @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif --}}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table
                class="table table-bordered table-striped {{ count($teams) > 0 ? 'datatable' : '' }} @can('team_delete') dt-select @endcan">
                <thead>
                    <tr>
                        @can('team_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /></th>
                        @endcan
                        <th>@lang('quickadmin.teams.fields.name')</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($teams) > 0)
                        @foreach ($teams as $team)
                            <tr data-entry-id="{{ $team->id }}">
                                @can('team_delete')
                                    <td></td>
                                @endcan
                                <td field-key="name">{{ $team->name }}</td>
                                <td>
                                    @can('team_view')
                                        <a href="{{ route('admin.teams.show', [$team->id]) }}"
                                            class="btn btn-xs btn-primary">@lang('quickadmin.qa_view')</a>
                                    @endcan
                                    @can('team_edit')
                                        <a href="{{ route('admin.teams.edit', [$team->id]) }}"
                                            class="btn btn-xs btn-info">@lang('quickadmin.qa_edit')</a>
                                    @endcan
                                    @can('team_delete')
                                        {!! Form::open([
                                            'style' => 'display: inline-block;',
                                            'method' => 'DELETE',
                                            'onsubmit' => "return confirm('" . trans('quickadmin.qa_are_you_sure') . "');",
                                            'route' => ['admin.teams.destroy', $team->id],
                                        ]) !!}
                                        {!! Form::submit(trans('quickadmin.qa_delete'), ['class' => 'btn btn-xs btn-danger']) !!}
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
@stop

@section('javascript')
    <script>
        @can('team_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.teams.mass_destroy') }}';

            $('#massDelete').on('click', function(e) {
                e.preventDefault();
                var ids = $.map($('.dt-select input:checked'), function(c) {
                    return $(c).closest('tr').data('entry-id');
                });

                if (ids.length === 0) {
                    alert('Please select at least one team to delete.');
                    return;
                }

                if (confirm('Are you sure you want to delete selected teams?')) {
                    $.ajax({
                        method: 'POST',
                        url: window.route_mass_crud_entries_destroy,
                        data: { ids: ids, _method: 'DELETE', _token: '{{ csrf_token() }}' },
                        success: function() {
                            location.reload();
                        },
                        error: function() {
                            alert('Something went wrong. Please try again.');
                        }
                    });
                }
            });
        @endcan
    </script>
@endsection
