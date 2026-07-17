@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('css')
<style>
    .welcome-card {
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: var(--box-shadow);
    }
    
    .welcome-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .welcome-subtitle {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }
    
    .dashboard-card {
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        overflow: hidden;
        background: white;
        border: none;
    }
    
    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .dashboard-btn {
        width: 100%;
        height: 100%;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        text-decoration: none;
        color: white;
        font-weight: 500;
        border-radius: var(--border-radius);
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.1);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .dashboard-btn:hover::before {
        opacity: 1;
    }
    
    .dashboard-btn:hover {
        color: white;
        transform: translateY(-2px);
    }
    
    .dashboard-btn i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        transition: transform 0.3s ease;
    }
    
    .dashboard-btn:hover i {
        transform: scale(1.1);
    }
    
    .dashboard-btn span {
        font-size: 0.95rem;
        text-align: center;
        line-height: 1.3;
    }
    
    .featured-btn {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        min-height: 100px;
    }
    
    .featured-btn .btn-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }
    
    .featured-btn .btn-text {
        text-align: left;
    }
    
    .featured-btn .btn-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .featured-btn .btn-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .btn-primary-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .btn-success-custom { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
    .btn-warning-custom { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
    .btn-info-custom { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }
    .btn-secondary-custom { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
    .btn-danger-custom { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); }
    
    @media (max-width: 768px) {
        .welcome-card {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .welcome-title {
            font-size: 1.25rem;
        }
        
        .dashboard-btn {
            min-height: 120px;
            padding: 1rem;
        }
        
        .dashboard-btn i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-btn span {
            font-size: 0.875rem;
        }
        
        .featured-btn {
            min-height: 80px;
        }
        
        .featured-btn .btn-content {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .featured-btn .btn-text {
            text-align: center;
        }
        
        .featured-btn .btn-title {
            font-size: 1.1rem;
        }
    }
    
    @media (max-width: 576px) {
        .welcome-card {
            padding: 1rem;
        }
        
        .dashboard-btn {
            min-height: 120px;
            padding: 0.75rem;
        }
        
        .dashboard-btn i {
            font-size: 1.75rem;
        }
        
        .dashboard-btn span {
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="welcome-title">
            <i class="bi bi-person-circle me-2"></i>Selamat Datang, {{ session('user')->nama }}
        </div>
        <p class="welcome-subtitle">Dashboard Admin - Kelola sistem EdoBest dengan mudah</p>
    </div>

    <!-- Featured Action: Operasional Harian -->
    <div class="dashboard-card">
        <a href="{{ route('operasional.index') }}" class="dashboard-btn featured-btn">
            <div class="btn-content">
                <i class="bi bi-clock-history"></i>
                <div class="btn-text">
                    <div class="btn-title">Operasional Harian</div>
                    <div class="btn-subtitle">Kelola aktivitas harian outlet</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Main Features Grid -->
    <div class="row g-2 g-md-3">
        <div class="col-6 col-md-4">
            <div class="dashboard-card">
                <a href="{{ route('pegawai.index') }}" class="dashboard-btn btn-primary-custom">
                    <i class="bi bi-people"></i>
                    <span>Kelola<br>Pegawai</span>
                </a>
            </div>
        </div>
        
        <div class="col-6 col-md-4">
            <div class="dashboard-card">
                <a href="{{ route('outlet.index') }}" class="dashboard-btn btn-primary-custom">
                    <i class="bi bi-shop"></i>
                    <span>Kelola<br>Outlet</span>
                </a>
            </div>
        </div>
        
        <div class="col-6 col-md-4">
            <div class="dashboard-card">
                <a href="{{ route('stok.index') }}" class="dashboard-btn btn-secondary-custom">
                    <i class="bi bi-box-seam"></i>
                    <span>Kelola<br>Stok</span>
                </a>
            </div>
        </div>
        
        <div class="col-6 col-md-4">
            <div class="dashboard-card">
                <a href="{{ route('harga.index') }}" class="dashboard-btn btn-success-custom">
                    <i class="bi bi-currency-dollar"></i>
                    <span>Kelola<br>Harga</span>
                </a>
            </div>
        </div>
        
        <div class="col-6 col-md-4">
            <div class="dashboard-card">
                <a href="{{ route('penggajian.index') }}" class="dashboard-btn btn-warning-custom">
                    <i class="bi bi-cash-stack"></i>
                    <span>Penggajian</span>
                </a>
            </div>
        </div>
        
        <div class="col-6 col-md-4">
            <div class="dashboard-card">
                <a href="{{ route('admin.kasbon.index') }}" class="dashboard-btn btn-info-custom">
                    <i class="bi bi-wallet2"></i>
                    <span>Kelola<br>Kasbon</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Full Width Report Button -->
    <div class="dashboard-card mt-3">
        <a href="{{ route('admin.rekap.index') }}" class="dashboard-btn btn-primary-custom" style="min-height: 80px;">
            <div class="btn-content">
                <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                <span style="font-size: 1.1rem;">Laporan Rekap Harian</span>
            </div>
        </a>
    </div>
</div>
@endsection
