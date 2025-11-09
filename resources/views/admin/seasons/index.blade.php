@extends('adminlte::page')

@section('title', 'Seasons Management')

@section('content_header')
    <h1>Seasons</h1>
@stop

@section('content')
    @livewire('admin.seasons-table')
@stop
