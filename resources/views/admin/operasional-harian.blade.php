@extends('layouts.app')

@section('title', 'Operasional Harian')

@section('css')
<style>
    .card {
        margin-bottom: 1rem;
    }
    .card-body p {
        margin-bottom: 0.5rem;
    }
    @media (max-width: 576px) {
        .card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4>Operasional Harian</h4>
            <p class="text-muted">Pilih outlet untuk melihat detail operasional</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Daftar Outlet -->
    <div class="row">
        @forelse ($outlets as $outlet)
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">{{ $outlet->nama }}</h6>
                                <p class="card-text small mb-1">Alamat: {{ $outlet->alamat }}</p>
                                <p class="card-text small mb-1">
                                    <strong>Status:</strong>
                                    @php
                                        $operasional = $outlet->operasionals->first();
                                        if ($operasional) {
                                            $rekap = $operasional->rekap;
                                            if ($rekap && $rekap->status === 'validated') {
                                                $statusText = 'Selesai';
                                                $badgeClass = 'primary';
                                            } else {
                                                $statusText = ucfirst($operasional->status);
                                                $badgeClass = $operasional->status === 'aktif' ? 'success' : 'danger';
                                            }
                                        } else {
                                            $statusText = 'Tutup';
                                            $badgeClass = 'danger';
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                                </p>
                                @if(isset($rekap))
                                @php
                                    $cashInfo = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0);
                                @endphp
                                <p class="card-text small mb-1">
                                    <strong>Cash di Pegawai:</strong> Rp {{ number_format($cashInfo, 0, ',', '.') }}
                                </p>
                                @endif
                            </div>
                            <a href="{{ route('operasional.detail', $outlet->id) }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-eye fs-4 me-2"></i>
                                <strong>Lihat Detail</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-shop display-1 text-muted"></i>
                    <p class="mt-3 text-muted">Belum ada outlet.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection