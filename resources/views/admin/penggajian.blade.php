@extends('layouts.app')

@section('title', 'Penggajian Pegawai')

@section('css')
<style>
    .pegawai-card {
        transition: transform 0.2s;
    }
    .pegawai-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('back-button')
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4>Penggajian Pegawai</h4>
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

    <!-- Daftar Pegawai -->
    <div class="row">
        @forelse ($pegawai as $p)
            <div class="col-12 mb-3">
                <div class="card pegawai-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">{{ $p->nama }}</h6>
                                <p class="card-text small mb-1">Username: {{ $p->username }}</p>
                                <p class="card-text small mb-1">Outlet: {{ $p->outlet->nama ?? 'Belum ditugaskan' }}</p>
                                <p class="card-text small mb-1">Gaji Harian: Rp {{ number_format($p->gaji_harian, 0, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('penggajian.show', $p->id) }}" class="btn btn-primary btn-lg">
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
                    <i class="bi bi-people display-1 text-muted"></i>
                    <p class="mt-3 text-muted">Belum ada pegawai terdaftar.</p>
                    <a href="{{ route('pegawai.index') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-plus-circle fs-4 me-2"></i>
                        <strong>Kelola Pegawai</strong>
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
