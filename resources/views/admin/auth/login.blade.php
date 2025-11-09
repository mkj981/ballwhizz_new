@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Admin Login')

@section('auth_body')
    <form action="{{ route('admin.login.post') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
        </div>
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>
        </div>
    </form>
@endsection
