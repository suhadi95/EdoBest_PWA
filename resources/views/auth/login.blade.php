@extends('layouts.app')

@section('title', 'Login')

@section('css')
<style>
    body {
        background:
            radial-gradient(1200px 500px at 10% -10%, rgba(87, 70, 235, 0.16), transparent 55%),
            radial-gradient(900px 400px at 100% 0%, rgba(61, 47, 196, 0.12), transparent 50%),
            #f5f6fa;
    }

    .login-wrap {
        max-width: 420px;
        margin: 0 auto;
        min-height: calc(100vh - 90px);
        display: flex;
        align-items: center;
        padding: 1.5rem 0.5rem;
    }

    .login-card {
        width: 100%;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    }

    .login-card__top {
        padding: 1.75rem 1.5rem 1.25rem;
        text-align: center;
        border-bottom: 1px solid #f0f1f4;
    }

    .login-card__logo {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        overflow: hidden;
        margin: 0 auto 0.9rem;
        box-shadow: 0 8px 20px rgba(87, 70, 235, 0.25);
    }

    .login-card__logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .login-card__top h1 {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0 0 0.25rem;
        letter-spacing: -0.02em;
    }

    .login-card__top p {
        margin: 0;
        color: var(--muted);
        font-size: 0.875rem;
    }

    .login-card__body {
        padding: 1.5rem;
    }

    .login-submit {
        width: 100%;
        margin-top: 0.35rem;
        padding: 0.85rem 1rem;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="login-wrap">
    <div class="login-card">
        <div class="login-card__top">
            <div class="login-card__logo">
                <img src="{{ asset('logo.png') }}" alt="EdoBest">
            </div>
            <h1>EdoBest</h1>
            <p>Masuk untuk mengelola outlet</p>
        </div>

        <div class="login-card__body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text"
                           class="form-control"
                           id="username"
                           name="username"
                           placeholder="Masukkan username"
                           required
                           autocomplete="username"
                           autofocus>
                </div>

                <button type="submit" class="btn btn-primary login-submit">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
