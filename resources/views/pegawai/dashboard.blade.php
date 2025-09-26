@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-person me-2"></i>Dashboard Pegawai</h5>
                <p>Status operasional outlet Anda hari ini.</p>
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
                @forelse ($outlets as $outlet)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6>{{ $outlet->nama }}</h6>
                            <p><strong>Alamat:</strong> {{ $outlet->alamat }}</p>
                            <p><strong>Status Operasional:</strong> 
                                <span class="badge bg-{{ $outlet->operasionals->isNotEmpty() ? 'success' : 'danger' }}">
                                    {{ $outlet->operasionals->isNotEmpty() ? 'Aktif' : 'Tutup' }}
                                </span>
                            </p>
                            @if ($outlet->operasionals->isNotEmpty())
                                <a href="{{ route('pegawai.penjualan', $outlet->id) }}" class="btn btn-primary btn-sm">Ke Halaman Penjualan</a>
                            @else
                                <p class="text-muted">Operasional belum dimulai oleh admin.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center">Anda belum ditugaskan ke outlet mana pun.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection