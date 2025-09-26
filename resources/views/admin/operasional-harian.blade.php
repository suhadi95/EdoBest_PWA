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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-clock me-2"></i>Operasional Harian</h5>
                <p>Pilih outlet untuk melihat detail operasional.</p>
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
                    <div class="card">
                        <div class="card-body">
                            <h6>{{ $outlet->nama }}</h6>
                            <p><strong>Alamat:</strong> {{ $outlet->alamat }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-{{ $outlet->operasionals->isNotEmpty() ? 'success' : 'danger' }}">
                                    {{ $outlet->operasionals->isNotEmpty() ? 'Aktif' : 'Tutup' }}
                                </span>
                            </p>
                            <a href="{{ route('operasional.detail', $outlet->id) }}" class="btn btn-primary btn-sm">Detail Operasional</a>
                        </div>
                    </div>
                @empty
                    <p class="text-center">Belum ada outlet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection