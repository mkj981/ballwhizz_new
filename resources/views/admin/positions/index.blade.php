@extends('adminlte::page')

@section('title', 'Positions Management')

@section('content_header')
    <h1>Positions Management</h1>
@stop

@section('content')
    @livewire('admin.positions-table')
@stop
