<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - EdoBest</title>

    {{-- PWA --}}
    <meta name="description" content="Aplikasi manajemen outlet EdoBest untuk penjualan, stok, rekap, kasbon, dan penggajian.">
    <meta name="theme-color" content="#5746eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EdoBest">
    <meta name="application-name" content="EdoBest">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @yield('css')
    <style>
        :root {
            --brand: #5746eb;
            --brand-dark: #3d2fc4;
            --primary-color: #5746eb;
            --primary-gradient: linear-gradient(135deg, #5746eb 0%, #3d2fc4 100%);
            --secondary-color: #6b7280;
            --success-color: #198754;
            --warning-color: #d97706;
            --danger-color: #dc3545;
            --info-color: #2563eb;
            --light-color: #f8f9fc;
            --dark-color: #1f2937;
            --border: #e8eaef;
            --muted: #9ca3af;
            --border-radius: 14px;
            --border-radius-sm: 10px;
            --box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
            --box-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f6fa;
            padding-top: 70px;
            line-height: 1.55;
            color: var(--dark-color);
        }

        .navbar {
            background: var(--primary-gradient) !important;
            box-shadow: 0 4px 16px rgba(87, 70, 235, 0.22);
            z-index: 1030;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.35rem;
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.92) !important;
            font-weight: 500;
            border-radius: 8px;
            margin: 0 2px;
            padding: 8px 12px !important;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.12);
        }

        .nav-link.btn {
            background: none;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .nav-link.btn:hover {
            background-color: rgba(255,255,255,0.18);
            border-color: rgba(255,255,255,0.5);
        }

        .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
            padding: 0.4rem;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.55rem 0.75rem;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background: #f3f4ff;
            color: var(--brand);
        }

        .container, .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .card {
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: none;
            background: white;
        }

        .card:hover { transform: none; }

        .card-body { padding: 1.25rem; }

        .btn {
            border-radius: var(--border-radius-sm);
            font-weight: 550;
            padding: 0.65rem 1.15rem;
            font-size: 0.925rem;
        }

        .btn:hover { transform: none; }

        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            color: white;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: white;
        }

        .btn-outline-primary {
            color: var(--brand);
            border-color: #c7c2f8;
            background: #fff;
        }

        .btn-outline-primary:hover {
            background: #f3f4ff;
            border-color: var(--brand);
            color: var(--brand-dark);
        }

        .btn-lg {
            padding: 0.85rem 1.35rem;
            font-size: 1rem;
            border-radius: var(--border-radius);
        }

        .btn-sm {
            padding: 0.4rem 0.75rem;
            font-size: 0.825rem;
        }

        .alert {
            border: none;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1.25rem;
        }

        .form-control, .form-select {
            border-radius: var(--border-radius-sm);
            border: 1px solid #e1e5e9;
            padding: 0.7rem 0.95rem;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 0.2rem rgba(87, 70, 235, 0.18);
        }

        .form-label {
            font-weight: 550;
            color: var(--dark-color);
            margin-bottom: 0.4rem;
        }

        .table {
            background: white;
            margin-bottom: 0;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            background: white;
        }

        .table th {
            background-color: #f8f9fc;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--dark-color);
            padding: 0.85rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .table td {
            border-bottom: 1px solid #f0f1f4;
            padding: 0.9rem 1rem;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fafbfd;
        }

        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 16px 48px rgba(15, 23, 42, 0.18);
        }

        .modal-header, .modal-body, .modal-footer {
            padding: 1.15rem 1.25rem;
        }

        .badge {
            font-weight: 550;
            padding: 0.4rem 0.65rem;
            border-radius: 7px;
        }

        /* ===== Shared UI kit ===== */
        .ui-page {
            max-width: 920px;
            margin: 0 auto;
            padding-bottom: 2rem;
        }

        .ui-page--wide { max-width: 1100px; }

        .ui-back {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--secondary-color);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .ui-back:hover { color: var(--brand); }

        .ui-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.35rem;
            padding-bottom: 1.1rem;
            border-bottom: 1px solid var(--border);
        }

        .ui-header h1 {
            font-size: 1.3rem;
            font-weight: 650;
            margin: 0 0 0.2rem;
            letter-spacing: -0.02em;
            color: var(--dark-color);
        }

        .ui-header p {
            margin: 0;
            color: var(--muted);
            font-size: 0.875rem;
        }

        .ui-header__meta {
            flex-shrink: 0;
            text-align: right;
            font-size: 0.8rem;
            color: var(--muted);
            line-height: 1.35;
        }

        .ui-header__meta strong {
            display: block;
            color: var(--brand);
            font-size: 0.95rem;
        }

        .ui-section { margin-bottom: 1.35rem; }

        .ui-section__title {
            font-size: 0.72rem;
            font-weight: 650;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin: 0 0 0.7rem 0.1rem;
        }

        .ui-primary {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            width: 100%;
            padding: 1.05rem 1.15rem;
            margin-bottom: 1.35rem;
            text-decoration: none;
            color: #fff;
            background: var(--primary-gradient);
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 8px 22px rgba(87, 70, 235, 0.28);
            text-align: left;
            cursor: pointer;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .ui-primary:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 10px 26px rgba(87, 70, 235, 0.34);
        }

        .ui-primary__icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .ui-primary__body { flex: 1; min-width: 0; }

        .ui-primary__body strong {
            display: block;
            font-size: 1rem;
            font-weight: 650;
            margin-bottom: 0.1rem;
        }

        .ui-primary__body span {
            font-size: 0.82rem;
            opacity: 0.9;
        }

        .ui-menu {
            background: #fff;
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .ui-menu__item {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.9rem 1.05rem;
            text-decoration: none;
            color: inherit;
            border-bottom: 1px solid #f0f1f4;
            background: #fff;
            width: 100%;
            border-left: 0;
            border-right: 0;
            border-top: 0;
            text-align: left;
            cursor: pointer;
        }

        .ui-menu__item:last-child { border-bottom: none; }
        .ui-menu__item:hover { background: #f8f9fc; color: inherit; }

        .ui-menu__icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .ui-icon--violet { background: #eef0fe; color: #5746eb; }
        .ui-icon--teal { background: #e6f7f5; color: #0d9488; }
        .ui-icon--amber { background: #fff7e6; color: #d97706; }
        .ui-icon--rose { background: #fef1f4; color: #e11d48; }
        .ui-icon--sky { background: #eef6ff; color: #2563eb; }
        .ui-icon--slate { background: #f1f5f9; color: #475569; }
        .ui-icon--green { background: #ecfdf5; color: #059669; }

        .ui-menu__text { flex: 1; min-width: 0; }

        .ui-menu__text strong {
            display: block;
            font-size: 0.94rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.1rem;
        }

        .ui-menu__text span {
            font-size: 0.78rem;
            color: var(--muted);
        }

        .ui-menu__chevron {
            color: #cbd5e1;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .ui-menu__item:hover .ui-menu__chevron { color: var(--brand); }

        .ui-menu__actions {
            display: flex;
            gap: 0.4rem;
            flex-shrink: 0;
        }

        .ui-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 550;
            background: #f1f5f9;
            color: #475569;
        }

        .ui-chip--violet { background: #eef0fe; color: #5746eb; }
        .ui-chip--green { background: #ecfdf5; color: #059669; }
        .ui-chip--amber { background: #fff7e6; color: #d97706; }
        .ui-chip--rose { background: #fef1f4; color: #e11d48; }
        .ui-chip--sky { background: #eef6ff; color: #2563eb; }

        .ui-panel {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.15rem;
            margin-bottom: 1.15rem;
        }

        .ui-empty {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--muted);
        }

        .ui-empty i {
            font-size: 2.5rem;
            display: block;
            margin-bottom: 0.75rem;
            opacity: 0.55;
        }

        .ui-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .ui-stat {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.9rem 1rem;
        }

        .ui-stat span {
            display: block;
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        .ui-stat strong {
            font-size: 1.15rem;
            font-weight: 650;
            color: var(--dark-color);
        }

        .bg-gradient-primary { background: var(--primary-gradient); }
        .text-gradient {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @media (max-width: 768px) {
            body { padding-top: 60px; }
            .navbar-brand { font-size: 1.2rem; }
            .nav-link { font-size: 0.9rem; padding: 6px 10px !important; }
            .container, .container-fluid {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            .card-body { padding: 1rem; }
            .ui-header { flex-direction: column; gap: 0.4rem; }
            .ui-header__meta { text-align: left; }
            .ui-header h1 { font-size: 1.15rem; }
            .table th, .table td {
                padding: 0.7rem 0.55rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .container, .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            .ui-menu__item { padding: 0.8rem 0.9rem; }
            .ui-primary { padding: 0.95rem 1rem; }
            .table th, .table td {
                white-space: nowrap;
                min-width: 90px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <img src="{{ asset('logo.png') }}" alt="EdoBest" width="32" height="32" class="rounded" style="object-fit: cover;">
                <span>EdoBest</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @if (session('user'))
                        @if (session('user')->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-grid me-1"></i>Menu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('operasional.index') }}">
                                    <i class="bi bi-clock-history me-1"></i>Operasional
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Lainnya
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><h6 class="dropdown-header">Data Master</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('pegawai.index') }}"><i class="bi bi-people me-2"></i>Pegawai</a></li>
                                    <li><a class="dropdown-item" href="{{ route('outlet.index') }}"><i class="bi bi-shop me-2"></i>Outlet</a></li>
                                    <li><a class="dropdown-item" href="{{ route('stok.index') }}"><i class="bi bi-box-seam me-2"></i>Stok</a></li>
                                    <li><a class="dropdown-item" href="{{ route('harga.index') }}"><i class="bi bi-tags me-2"></i>Harga</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><h6 class="dropdown-header">Keuangan & Laporan</h6></li>
                                    <li><a class="dropdown-item" href="{{ route('penggajian.index') }}"><i class="bi bi-cash-stack me-2"></i>Penggajian</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.kasbon.index') }}"><i class="bi bi-wallet2 me-2"></i>Kasbon</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.rekap.index') }}"><i class="bi bi-file-earmark-text me-2"></i>Laporan Rekap</a></li>
                                </ul>
                            </li>
                        @elseif (session('user')->role === 'pegawai')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pegawai.dashboard') }}">
                                    <i class="bi bi-grid me-1"></i>Menu
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Lainnya
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('pegawai.histori-gaji.index') }}"><i class="bi bi-receipt me-2"></i>Histori Gaji</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pegawai.histori-rekap.index') }}"><i class="bi bi-clock-history me-2"></i>Histori Rekap</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pegawai.kasbon.index') }}"><i class="bi bi-wallet2 me-2"></i>Kasbon</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pegawai.listrik.index') }}"><i class="bi bi-lightning-charge me-2"></i>Catatan Listrik</a></li>
                                </ul>
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

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset('sw.js') }}', { scope: '/' })
                    .then(function (registration) {
                        if (registration.waiting) {
                            registration.waiting.postMessage({ type: 'SKIP_WAITING' });
                        }
                        registration.addEventListener('updatefound', function () {
                            var newWorker = registration.installing;
                            if (!newWorker) return;
                            newWorker.addEventListener('statechange', function () {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                                }
                            });
                        });
                    })
                    .catch(function (error) {
                        console.warn('Service Worker gagal terdaftar:', error);
                    });
            });
        }
    </script>
</body>
</html>