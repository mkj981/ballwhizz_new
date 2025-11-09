@extends('adminlte::page')

@section('title', 'Leagues Management')

@section('content_header')
    <h1>Leagues</h1>
@stop

@section('content')
    @livewire('admin.leagues-table')
@stop
