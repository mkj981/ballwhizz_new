@extends('adminlte::page')

@section('title', 'Players Management')

@section('content_header')
    <h1>Players Management</h1>
@stop

@section('content')
    @livewire('admin.players-table')
@stop
