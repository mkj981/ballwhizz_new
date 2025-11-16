@extends('adminlte::page')

@section('title', 'App News')

@section('content_header')
    <h1>📰 App News</h1>
@stop

@section('content')
    <livewire:admin.app-news-table />
@stop
