@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.teams.title')</h3>
    
    {!! Form::model($team, ['method' => 'PUT', 'route' => ['admin.teams.update', $team->id]]) !!}

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_edit')
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('name', trans('quickadmin.teams.fields.name').'*', ['class' => 'control-label']) !!}
                    {!! Form::text('name', old('name'), ['class' => 'form-control', 'placeholder' => '', 'required' => '']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('name'))
                        <p class="help-block">
                            {{ $errors->first('name') }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 form-group">
                    {!! Form::label('manager_id', 'Team Manager', ['class' => 'control-label']) !!}
                    {!! Form::select('manager_id', $users, old('manager_id', $team->manager_id), ['class' => 'form-control select2', 'placeholder' => 'Select a Manager']) !!}
                    <p class="help-block"></p>
                    @if($errors->has('manager_id'))
                        <p class="help-block">
                            {{ $errors->first('manager_id') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
<a href="{{ url("/admin/teams") }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i>
        Back to Teams
    </a>
    {!! Form::submit(trans('quickadmin.qa_update'), ['class' => 'btn btn-danger']) !!}
    {!! Form::close() !!}
@stop