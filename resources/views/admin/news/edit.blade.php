@extends('adminlte::page')
@section('title', 'Edit News')
@section('content_header')
    <h1>✏️ Edit News</h1>
@stop
@section('content')
    <form action="{{ route('admin.news.update', $news->id) }}" method="POST" enctype="multipart/form-data">


    @csrf
        @method('PUT')
        @include('admin.news.form')
    </form>
@stop
