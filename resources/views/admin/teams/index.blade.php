@extends('adminlte::page')

@section('title', 'Teams Management')

@section('content_header')
    <h1>⚽ Teams Management</h1>
@stop

@section('content')
    @livewire('admin.teams-table')
@stop
