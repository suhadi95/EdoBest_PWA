@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('css')
<style>
    .dashboard-card {
        margin-bottom: 1rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
    }
    .dashboard-card .card-body {
        padding: 1.25rem;
    }
    .status-badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    .quick-action-btn {
        width: 100%;
        padding: 0.75rem;
        font-size: 1rem;
        border-radius: 8px;
        margin-bottom: 0.5rem;
    }
    .quick-action-btn.btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.125rem;
        border-radius: 0.375rem;
    }
    .outlet-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .outlet-name {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .outlet-address {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    @media (max-width: 576px) {
        .dashboard-card .card-body {
            padding: 1rem;
        }
        .quick-action-btn {
            font-size: 1rem;
            padding: 1rem 1.25rem;
            min-height: 48px;
        }
        .outlet-name {
            font-size: 1.1rem;
        }
        .row.g-2 {
            --bs-gutter-x: 0.75rem;
            --bs-gutter-y: 0.75rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Outlet Information -->
    @if ($outlet)
        @php
            $outlets = [$outlet];
        @endphp
        @forelse ($outlets as $outlet)
            <div class="outlet-info">
                <div class="outlet-name">
                    <i class="bi bi-shop me-2"></i>{{ $outlet->nama }}
                </div>
                <div class="outlet-address">
                    <i class="bi bi-geo-alt me-1"></i>{{ $outlet->alamat }}
                </div>
                <div class="mt-2">
                    @php
                        $operasional = $outlet->operasionals->first();
                        $isSelesai = false;
                        $rekapSudahAda = false;
                        if ($operasional) {
                            $rekap = $operasional->rekap;
                            if ($rekap) {
                                $rekapSudahAda = true;
                                if ($rekap->status === 'validated') {
                                    $statusText = 'Selesai';
                                    $badgeClass = 'primary';
                                    $isSelesai = true;
                                } else {
                                    $statusText = 'Menunggu Validasi';
                                    $badgeClass = 'warning';
                                }
                            } else {
                                $statusText = ucfirst($operasional->status);
                                $badgeClass = $operasional->status === 'aktif' ? 'success' : 'danger';
                            }
                        } else {
                            $statusText = 'Tutup';
                            $badgeClass = 'danger';
                        }
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} status-badge">
                        <i class="bi bi-circle-fill me-1"></i>{{ $statusText }}
                    </span>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                <p class="mt-3 text-muted">Anda belum ditugaskan ke outlet mana pun.</p>
            </div>
        @endforelse
    @else
        <div class="text-center py-5">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">Anda belum ditugaskan ke outlet mana pun.</p>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="row g-2 g-md-3">
        @if ($outlet && $operasional && $operasional->status === 'aktif')
            <div class="col-12 col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-cart-plus text-primary" style="font-size: 2rem;"></i>
                        <a href="{{ route('pegawai.penjualan', $outlet->id) }}" class="btn btn-primary quick-action-btn mb-0 d-flex align-items-center">
                            <i class="bi bi-play-circle me-2"></i>Mulai Penjualan
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($outlet && $operasional && $operasional->status === 'aktif' && !$rekapSudahAda)
            <div class="col-12 col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-file-earmark-text text-warning" style="font-size: 2rem;"></i>
                        <a href="{{ route('pegawai.rekap', $outlet->id) }}" class="btn btn-warning quick-action-btn mb-0 d-flex align-items-center">
                            <i class="bi bi-clipboard-check me-2"></i>Buat Rekap
                        </a>
                    </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12 col-md-6">
                <div class="dashboard-card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-file-earmark-check text-primary" style="font-size: 2rem;"></i>
                            <a href="{{ route('pegawai.histori-rekap.index') }}" class="btn btn-primary quick-action-btn mb-0 d-flex align-items-center">
                                <i class="bi bi-clock-history me-2"></i>Histori Rekap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tombol Buat Rekap Hari Sebelumnya -->
        @if ($outlet)
            @php
                // Cek apakah ada operasional yang:
                // 1. Belum memiliki rekap
                // 2. Memiliki transaksi (ada aktivitas operasional)
                // 3. Status aktif atau selesai
                // 4. Maksimal 30 hari terakhir
                $hasOperasionalWithoutRekap = \App\Models\Operasional::where('outlet_id', $outlet->id)
                    ->where('tanggal', '>=', \Carbon\Carbon::now()->subDays(30)->toDateString())
                    ->whereDoesntHave('rekap')
                    ->whereHas('transaksis')
                    ->whereIn('status', ['aktif', 'selesai'])
                    ->exists();
            @endphp
            @if ($hasOperasionalWithoutRekap)
                <div class="col-12 col-md-6">
                    <div class="dashboard-card h-100" style="border: 2px solid var(--warning-color);">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-calendar-x text-warning" style="font-size: 2rem;"></i>
                                <a href="{{ route('pegawai.pilih-tanggal-rekap', $outlet->id) }}" class="btn btn-warning quick-action-btn mb-0 d-flex align-items-center">
                                    <i class="bi bi-calendar-plus me-2"></i>Buat Rekap Lama
                                </a>
                            </div>
                            <small class="text-muted mt-2 d-block">Ada rekap yang belum dibuat</small>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        
        <!-- Tombol yang selalu muncul -->
        <div class="col-12 col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-cash text-info" style="font-size: 2rem;"></i>
                        <a href="{{ route('pegawai.kasbon.index') }}" class="btn btn-info quick-action-btn mb-0 d-flex align-items-center">
                            <i class="bi bi-plus-circle me-2"></i>Ajukan Kasbon
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-receipt text-success" style="font-size: 2rem;"></i>
                        <a href="{{ route('pegawai.histori-gaji.index') }}" class="btn btn-success quick-action-btn mb-0 d-flex align-items-center">
                            <i class="bi bi-eye me-2"></i>Histori Gaji
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
