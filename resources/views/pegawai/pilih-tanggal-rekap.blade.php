@extends('layouts.app')

@section('title', 'Pilih Tanggal Rekap - {{ $outlet->nama }}')

@section('css')
    <style>
        .page-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: var(--box-shadow);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .page-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 0.5rem;
            margin-bottom: 0;
        }

        .info-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid var(--info-color);
        }

        .operasional-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .operasional-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
        }

        .operasional-info {
            flex: 1;
        }

        .operasional-date {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .operasional-details {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--secondary-color);
        }

        .detail-item i {
            color: var(--primary-color);
        }

        .btn-buat-rekap {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-buat-rekap:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .operasional-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .operasional-details {
                gap: 1rem;
            }

            .btn-buat-rekap {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('back-button')
<a href="{{ route('pegawai.dashboard') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-calendar-check"></i>
                Pilih Tanggal Rekap
            </h1>
            <p class="page-subtitle">
                <i class="bi bi-shop me-1"></i>{{ $outlet->nama }} • <i class="bi bi-geo-alt me-1"></i>{{ $outlet->alamat }}
            </p>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Info Card -->
        <div class="info-card">
            <h6 style="margin: 0 0 0.5rem 0; color: var(--info-color); font-weight: 600;">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </h6>
            <p style="margin: 0; font-size: 0.9rem; color: var(--secondary-color);">
                Berikut adalah daftar hari operasional yang memiliki transaksi namun belum memiliki laporan rekap. Pilih tanggal untuk membuat laporan rekap yang terlewat.
            </p>
        </div>

        <!-- List Operasional -->
        @if ($operasionalsWithoutRekap->count() > 0)
            @foreach ($operasionalsWithoutRekap as $operasional)
                <div class="operasional-card">
                    <div class="operasional-info">
                        <div class="operasional-date">
                            <i class="bi bi-calendar3 me-2"></i>
                            {{ \Carbon\Carbon::parse($operasional->tanggal)->format('d F Y') }}
                        </div>
                        <div class="operasional-details">
                            <div class="detail-item">
                                <i class="bi bi-clock"></i>
                                <span>{{ \Carbon\Carbon::parse($operasional->tanggal)->diffForHumans() }}</span>
                            </div>
                            <div class="detail-item">
                                <i class="bi bi-circle-fill" style="font-size: 0.5rem; color: {{ $operasional->status === 'aktif' ? 'var(--success-color)' : 'var(--secondary-color)' }}"></i>
                                <span>Status: {{ ucfirst($operasional->status) }}</span>
                            </div>
                            @php
                                $transaksiCount = \App\Models\Transaksi::where('operasional_id', $operasional->id)->count();
                            @endphp
                            <div class="detail-item">
                                <i class="bi bi-receipt"></i>
                                <span>{{ $transaksiCount }} Transaksi</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('pegawai.rekap', ['outlet_id' => $outlet->id, 'tanggal' => $operasional->tanggal]) }}" class="btn-buat-rekap">
                        <i class="bi bi-file-earmark-plus"></i>
                        Buat Rekap
                    </a>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <i class="bi bi-check-circle"></i>
                <h5 style="margin-bottom: 0.5rem;">Semua Rekap Sudah Dibuat</h5>
                <p style="margin: 0;">Tidak ada operasional yang memerlukan laporan rekap saat ini.</p>
                <a href="{{ route('pegawai.dashboard') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-house-door me-2"></i>Kembali ke Dashboard
                </a>
            </div>
        @endif
    </div>
@endsection

