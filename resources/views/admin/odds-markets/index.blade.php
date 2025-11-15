@extends('adminlte::page')

@section('title', 'Odds Markets')

@section('content_header')
    <h1>🎯 Odds Markets</h1>
@stop

@section('content')
    <livewire:admin.odds-markets-table />
@stop
@section('css')
    <style>
        .card-header {
            border-bottom: 2px solid #f5c542 !important;
        }

        .search-box {
            width: 260px;
            position: relative;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
            font-size: 13px;
        }

        .search-box input {
            border: none;
            border-radius: 6px;
            font-size: 13px;
            padding: 5px 10px;
            padding-left: 28px !important;
            background-color: #f8f9fa;
            transition: all 0.2s ease-in-out;
        }

        .search-box input:focus {
            background-color: #fff;
            border: 1px solid #f5c542;
            box-shadow: 0 0 0 0.2rem rgba(245, 197, 66, 0.25);
            outline: none;
        }
    </style>
@stop
