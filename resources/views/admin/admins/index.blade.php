@extends('adminlte::page')

@section('title', 'Admins')

@section('content_header')
    <h1>Admins</h1>
@stop

@section('content')
    <a href="{{ route('admin.admins.create') }}" class="btn btn-primary mb-3">Add Admin</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($admins as $admin)
            <tr>
                <td>{{ $admin->id }}</td>
                <td>{{ $admin->name }}</td>
                <td>{{ $admin->email }}</td>
                <td>{{ $admin->role->display_name ?? ucfirst($admin->role->name ?? '-') }}</td>
                <td>
                    <a href="{{ route('admin.admins.edit', $admin) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.admins.destroy', $admin) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this admin?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $admins->links() }}
@stop
