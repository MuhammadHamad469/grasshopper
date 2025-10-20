@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.teams.title')</h3>
    @can('team_create')
        <p>
            <a href="{{ route('admin.teams.create') }}" class="btn btn-success">@lang('quickadmin.qa_add_new')</a>

        </p>
    @endcan
    <h1>hello</h1>


    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            {{-- Talha code start --}}
            @if (session('success'))
                <div class="alert alert-danger">
                    {{ session('success') }}
                </div>
            @endif
            {{-- Talha code end --}}
            <table
                class="table table-bordered table-striped {{ count($teams) > 0 ? 'datatable' : '' }} @can('team_delete') dt-select @endcan">
                {{-- ===============sherry code here========= --}}
                <thead>
                    <tr>
                        @can('team_delete')
                            <th style="text-align:center;"><input type="checkbox" id="select-all" /><span
                                    style="display: none;">Selection</span></th>
                        @endcan

                        <th>@lang('quickadmin.teams.fields.name')</th>
                        <th>Manager</th>
                        <th><span style="display: none;">Action</span></th>
                    </tr>
                </thead>
                {{-- ===============sherry code here========= --}}
                {{-- ===============sherry code here========= --}}
                <tbody>
                    @if (count($teams) > 0)
                        @foreach ($teams as $team)
                            <tr data-entry-id="{{ $team->id }}" style="height: 40px !important;">
                                @can('team_delete')
                                    <td></td>
                                @endcan

                                <td field-key='name'>{{ $team->name }}</td>
                                <td>{{ $team->manager->name ?? 'No Manager' }}</td>
                                <td>
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
                {{-- ===============sherry code here========= --}}
            </table>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        @can('team_delete')
            window.route_mass_crud_entries_destroy = '{{ route('admin.teams.mass_destroy') }}';
        @endcan
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButton = document.querySelector('.btn-danger'); // or the exact class/id of the delete button
        deleteButton.addEventListener('click', function (e) {
            const selected = document.querySelectorAll('input[name="ids[]"]:checked');
            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one team to delete.');
                return false;
            }
        });
    });
</script>

@endsection
