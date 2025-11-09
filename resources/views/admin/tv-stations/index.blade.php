@extends('adminlte::page')

@section('title', 'TV Stations Management')

@section('content_header')
    <h1>TV Stations</h1>
@stop

@section('content')
    @livewire('admin.tv-stations-table')
@stop
