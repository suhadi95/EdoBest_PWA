@extends('layouts.app')

@section('title', 'Kasbon Admin')

@section('back-button')
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4>Kelola Kasbon</h4>
            <p class="text-muted">Pilih pegawai untuk melihat pengajuan kasbon dan histori</p>
        </div>
    </div>

    <!-- Daftar Pegawai -->
    <div class="row">
        @forelse($pegawai as $p)
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">{{ $p->nama }}</h6>
                                <p class="card-text small mb-0">Username: {{ $p->username }}</p>
                            </div>
                            <a href="{{ route('admin.kasbon.show', $p->id) }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-eye fs-4 me-2"></i>
                                <strong>Lihat Kasbon</strong>
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
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
