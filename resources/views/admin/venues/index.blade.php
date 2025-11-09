@extends('adminlte::page')

@section('title', 'Venues Management')

@section('content_header')
    <h1>Venues</h1>
@stop

@section('content')
    @livewire('admin.venues-table')
@stop
