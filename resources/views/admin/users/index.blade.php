@extends('adminlte::page')
@section('title', 'Users')
@section('content')
    @livewire('admin.users-table')
@endsection
@section('js') @livewireScripts @endsection
@section('css') @livewireStyles @endsection
