@extends('layouts.app')

@section('title', 'Login')

@section('css')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .login-container {
        max-width: 450px;
        margin: 0 auto;
        padding: 2rem 1rem;
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .login-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        overflow: hidden;
        width: 100%;
        border: none;
    }
    
    .login-header {
        background: var(--primary-gradient);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .login-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .login-subtitle {
        font-size: 0.95rem;
        opacity: 0.9;
        margin-top: 0.5rem;
        margin-bottom: 0;
    }
    
    .login-body {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-control {
        padding: 1rem;
        font-size: 1rem;
        border: 2px solid #e1e5e9;
        border-radius: var(--border-radius-sm);
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .form-control:focus {
        background: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }
    
    .login-btn {
        width: 100%;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: var(--border-radius-sm);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    
    .login-btn:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }
    
    .login-btn:active {
        transform: translateY(0);
    }
    
    .alert {
        border-radius: var(--border-radius-sm);
        border: none;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }
    
    .brand-logo {
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .login-container {
            padding: 1rem;
            min-height: calc(100vh - 60px);
        }
        
        .login-header {
            padding: 1.5rem;
        }
        
        .login-title {
            font-size: 1.5rem;
        }
        
        .login-body {
            padding: 1.5rem;
        }
        
        .form-control {
            padding: 0.875rem;
        }
        
        .login-btn {
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }
    }
    
    @media (max-width: 576px) {
        .login-container {
            padding: 0.5rem;
        }
        
        .login-header {
            padding: 1rem;
        }
        
        .login-title {
            font-size: 1.25rem;
        }
        
        .login-body {
            padding: 1rem;
        }
        
        .brand-logo {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <i class="bi bi-shop"></i>
            </div>
            <h1 class="login-title">
                EdoBest
            </h1>
            <p class="login-subtitle">Sistem Manajemen Donat Terbaik</p>
        </div>
        
        <div class="login-body">
            <!-- Alert Messages -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="bi bi-person-fill"></i>
                        Username
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Masukkan username Anda"
                           required 
                           autocomplete="username"
                           autofocus>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk ke Sistem</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection