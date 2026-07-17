@extends('layouts.app')

@section('title', 'Kelola Stok Kemasan')

@section('back-button')
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4>Kelola Stok Kemasan</h4>
            <p class="text-muted">Pilih outlet untuk melihat detail stok kemasan</p>
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
                                <p class="card-text small mb-0">Alamat: {{ $outlet->alamat }}</p>
                            </div>
                            <a href="{{ route('stok.detail', $outlet->id) }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-eye fs-4 me-2"></i>
                                <strong>Lihat Stok</strong>
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