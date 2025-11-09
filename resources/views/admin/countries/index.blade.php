@extends('adminlte::page')

@section('title', 'Countries')

@section('content_header')
    <h1 class="fw-bold text-dark">
        🌍 Countries Management
    </h1>
@stop

@section('content')
    {{-- Only include Livewire table (everything handled there) --}}
    <livewire:admin.countries-table />
@stop

@section('css')
    <style>
        /* 🌍 Card Header */
        .card-header {
            border-bottom: 2px solid #f5c542 !important;
        }

        /* 🔍 Search Box */
        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            margin-left: 20px;
            left: 10px;
            transform: translateY(-50%);
            color: #999;
            font-size: 13px;
        }

        .search-box input {
            border: none;
            margin-left: 20px;
            border-radius: 6px;
            font-size: 13px;
            padding: 6px 10px;
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

        /* 🧾 Table */
        .table td, .table th {
            vertical-align: middle !important;
        }

        /* 🔢 Pagination */
        .pagination {
            justify-content: center !important;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .pagination .page-link {
            padding: 8px 16px;
            font-size: 15px;
            font-weight: 500;
            color: #212529;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }

        .pagination .page-link:hover {
            background-color: #f8f9fa;
            border-color: #f5c542;
        }

        .pagination .page-item.active .page-link {
            background-color: #f5c542;
            border-color: #f5c542;
            color: #000;
            font-weight: 600;
            box-shadow: 0 0 6px rgba(245, 197, 66, 0.5);
        }

        /* 🌀 Hide Livewire’s default loading arrow */
        [wire\:loading] {
            display: none !important;
        }
    </style>
@stop
