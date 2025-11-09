@extends('adminlte::page')

@section('title', 'Boxes Types')

@section('content_header')
    <h1>🎁 Boxes Types Management</h1>
@stop

@section('content')
    <livewire:admin.boxes-types-table />
@stop
@section('css')
    <style>
        .upload-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .img-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .placeholder-box {
            width: 60px;
            height: 60px;
            border: 1px dashed #ccc;
            border-radius: 8px;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .custom-upload-btn {
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #333;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .custom-upload-btn:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .custom-upload-btn i {
            font-size: 11px;
        }

        .card-header {
            border-bottom: 2px solid #f5c542 !important; /* golden accent line */
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
            padding-left: 28px !important; /* space for icon */
            background-color: #f8f9fa;
            transition: all 0.2s ease-in-out;
        }

        .search-box input:focus {
            background-color: #fff;
            border: 1px solid #f5c542;
            box-shadow: 0 0 0 0.2rem rgba(245, 197, 66, 0.25);
            outline: none;
        }
        .card-title-spaced {
            margin-right: 2rem !important;
        }
    </style>
@stop
