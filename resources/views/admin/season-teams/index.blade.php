@extends('adminlte::page')

@section('title', 'Season Teams Management')

@section('content_header')
    <h1>Season Teams</h1>
@stop

@section('content')
    @livewire('admin.season-teams')
@stop
