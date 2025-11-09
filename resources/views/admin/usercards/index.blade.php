@extends('adminlte::page')

@section('title', 'User Cards')

@section('content_header')
    <h1>🃏 User Cards Management</h1>
@stop

@section('content')
    @livewire('admin.user-cards-table')
@stop
