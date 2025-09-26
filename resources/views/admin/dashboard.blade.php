@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Selamat Datang, {{ session('user')->nama }} (Admin)</h5>
                <p>Ini adalah dashboard admin. Anda telah berhasil login dengan username: {{ session('user')->username }}.</p>
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('pegawai.index') }}" class="btn btn-primary w-100">
                            <i class="bi bi-people me-2"></i>Kelola Pegawai
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('outlet.index') }}" class="btn btn-primary w-100">
                            <i class="bi bi-shop me-2"></i>Kelola Outlet
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('stok.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-box-seam me-2"></i>Kelola Stok Kemasan
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('operasional.index') }}" class="btn btn-info w-100">
                            <i class="bi bi-clock me-2"></i>Operasional Harian
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-6 mb-3">
                        <a href="{{ route('admin.rekap.index') }}" class="btn btn-primary w-100">
                            <i class="bi bi-file-text me-2"></i>Laporan Rekap Harian
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="{{ route('harga.index') }}" class="btn btn-success w-100">
                            <i class="bi bi-currency-dollar me-2"></i>Kelola Harga Item
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="#" class="btn btn-warning w-100">
                            <i class="bi bi-currency-dollar me-2"></i>Penggajian
                        </a>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="btn btn-danger mt-3"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection