@extends('adminlte::page')
@section('title', 'Add News')
@section('content_header')
    <h1>➕ Add New News</h1>
@stop
@section('content')
    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">


    @csrf
        @include('admin.news.form')
    </form>
@stop
