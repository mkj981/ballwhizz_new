@extends('adminlte::page')

@section('title', 'User Rankings')

@section('content_header')
    <h1>🏆 User Rankings</h1>
@stop

@section('content')
    <livewire:admin.users-rankings-table />
@stop
@section('css')
    <style>
        /* Spacing between select filters */
        .ranking-filter-select {
            margin-left: 6px;
            margin-right: 6px;
            min-width: 140px; /* optional: makes them same width */
        }

        /* Compact look inside dark header */
        .ranking-filter-select {
            background: #ffffff;
            color: #000;
            border-radius: 4px;
            padding: 3px 6px;
        }

        /* Optional: add hover effect */
        .ranking-filter-select:hover {
            border-color: #0d6efd;
        }
    </style>
@endsection
