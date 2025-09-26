@extends('layouts.app')

@section('title', 'Login')

@section('css')
<style>
    .login-container {
        max-width: 400px;
        margin: 50px auto;
    }
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    @media (max-width: 576px) {
        .login-container {
            margin: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-center"><i class="bi bi-lock me-2"></i>Login</h5>
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
@endsection