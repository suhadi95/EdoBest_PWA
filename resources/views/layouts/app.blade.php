<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - EdoBest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @yield('css')
    <style>
        :root {
            --primary-color: #667eea;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            --box-shadow-sm: 0 1px 4px rgba(0,0,0,0.1);
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f6fa;
            padding-top: 70px;
            line-height: 1.6;
        }
        
        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: var(--box-shadow);
            z-index: 1030;
            backdrop-filter: blur(10px);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 6px;
            margin: 0 2px;
            padding: 8px 12px !important;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            transform: translateY(-1px);
        }
        
        .nav-link.btn {
            background: none;
            border: 1px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        
        .nav-link.btn:hover {
            background-color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
        }
        
        /* Container improvements */
        .container, .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        /* Card improvements */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: all 0.3s ease;
            background: white;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        /* Button improvements */
        .btn {
            border-radius: var(--border-radius-sm);
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
        }
        
        .btn-lg {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: var(--border-radius);
        }
        
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }
        
        /* Alert improvements */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-sm);
            margin-bottom: 1.5rem;
        }
        
        /* Form improvements */
        .form-control, .form-select {
            border-radius: var(--border-radius-sm);
            border: 1px solid #e1e5e9;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        /* Table improvements */
        .table {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow-sm);
            background: white;
        }
        
        .table-responsive {
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
        }
        
        .table th {
            background-color: var(--light-color);
            border: none;
            font-weight: 600;
            color: var(--dark-color);
            padding: 1rem;
        }
        
        .table td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,0.02);
        }
        
        /* Modal improvements */
        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.5rem;
        }
        
        /* Badge improvements */
        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            
            .navbar-brand {
                font-size: 1.3rem;
            }
            
            .nav-link {
                font-size: 0.9rem;
                padding: 6px 10px !important;
            }
            
            .container, .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.75rem 1.25rem;
                font-size: 0.9rem;
            }
            
            .btn-lg {
                padding: 1rem 1.5rem;
                font-size: 1rem;
            }
            
            .modal-header, .modal-body, .modal-footer {
                padding: 1rem;
            }
            
            .table th, .table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f7fafc;
            }
            
            .table-responsive::-webkit-scrollbar {
                height: 8px;
            }
            
            .table-responsive::-webkit-scrollbar-track {
                background: #f7fafc;
                border-radius: 4px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 4px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
        }
        
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .container, .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .card-body {
                padding: 0.75rem;
            }
            
            .btn {
                font-size: 0.875rem;
                padding: 0.625rem 1rem;
            }
            
            .form-control, .form-select {
                font-size: 0.9rem;
                padding: 0.625rem 0.875rem;
            }
            
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e0 #f7fafc;
            }
            
            .table-responsive::-webkit-scrollbar {
                height: 6px;
            }
            
            .table-responsive::-webkit-scrollbar-track {
                background: #f7fafc;
                border-radius: 3px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb {
                background: #cbd5e0;
                border-radius: 3px;
            }
            
            .table-responsive::-webkit-scrollbar-thumb:hover {
                background: #a0aec0;
            }
            
            .table th, .table td {
                white-space: nowrap;
                min-width: 100px;
            }
        }
        
        /* Utility classes */
        .text-gradient {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .bg-gradient-primary {
            background: var(--primary-gradient);
        }
        
        .shadow-custom {
            box-shadow: var(--box-shadow);
        }
        
        .shadow-custom-sm {
            box-shadow: var(--box-shadow-sm);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">EdoBest</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (session('user'))
                        @if (session('user')->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pegawai.index') }}">Kelola Pegawai</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('outlet.index') }}">Kelola Outlet</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('operasional.index') }}">Operasional Harian</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('stok.index') }}">Kelola Stok</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('harga.index') }}">Kelola Harga</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('penggajian.index') }}">Penggajian</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.kasbon.index') }}">Kelola Kasbon</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.rekap.index') }}">Laporan Rekap</a>
                            </li>
                        @elseif (session('user')->role === 'pegawai')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pegawai.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pegawai.histori-gaji.index') }}">Histori Gaji</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pegawai.histori-rekap.index') }}">Histori Rekap</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pegawai.kasbon.index') }}">Kasbon</a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @yield('back-button')
        @yield('content')
    </div>

    @yield('modals')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('js')
</body>
</html>