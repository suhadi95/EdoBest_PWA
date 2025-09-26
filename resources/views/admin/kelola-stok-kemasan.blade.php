@extends('layouts.app')

@section('title', 'Kelola Stok Kemasan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-box-seam me-2"></i>Kelola Stok Kemasan</h5>
                <p>Pilih outlet untuk melihat detail stok kemasan.</p>
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
                            <p>Alamat: {{ $outlet->alamat }}</p>
                            <a href="{{ route('stok.detail', $outlet->id) }}" class="btn btn-primary btn-sm">Detail Stok</a>
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