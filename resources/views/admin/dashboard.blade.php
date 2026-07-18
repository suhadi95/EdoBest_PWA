@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
@php
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][now()->dayOfWeek];
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][now()->month];
@endphp

<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Halo, {{ session('user')->nama }}</h1>
            <p>Pilih menu untuk mulai mengelola EdoBest</p>
        </div>
        <div class="ui-header__meta">
            <strong>{{ $hari }}</strong>
            {{ now()->day }} {{ $bulan }} {{ now()->year }}
        </div>
    </header>

    <a href="{{ route('operasional.index') }}" class="ui-primary">
        <div class="ui-primary__icon"><i class="bi bi-clock-history"></i></div>
        <div class="ui-primary__body">
            <strong>Operasional Harian</strong>
            <span>Buka aktivitas outlet hari ini</span>
        </div>
        <i class="bi bi-arrow-right"></i>
    </a>

    <section class="ui-section">
        <h2 class="ui-section__title">Data Master</h2>
        <div class="ui-menu">
            <a href="{{ route('pegawai.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--violet"><i class="bi bi-people"></i></div>
                <div class="ui-menu__text">
                    <strong>Pegawai</strong>
                    <span>Tambah & kelola akun pegawai</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
            <a href="{{ route('outlet.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--teal"><i class="bi bi-shop"></i></div>
                <div class="ui-menu__text">
                    <strong>Outlet</strong>
                    <span>Data lokasi dan alamat outlet</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
            <a href="{{ route('stok.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--amber"><i class="bi bi-box-seam"></i></div>
                <div class="ui-menu__text">
                    <strong>Stok</strong>
                    <span>Kelola stok kemasan per outlet</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
            <a href="{{ route('harga.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--sky"><i class="bi bi-tags"></i></div>
                <div class="ui-menu__text">
                    <strong>Harga</strong>
                    <span>Atur harga item penjualan</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
        </div>
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Keuangan & Laporan</h2>
        <div class="ui-menu">
            <a href="{{ route('penggajian.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--amber"><i class="bi bi-cash-stack"></i></div>
                <div class="ui-menu__text">
                    <strong>Penggajian</strong>
                    <span>Hitung dan catat gaji pegawai</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
            <a href="{{ route('admin.kasbon.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--rose"><i class="bi bi-wallet2"></i></div>
                <div class="ui-menu__text">
                    <strong>Kasbon</strong>
                    <span>Persetujuan & riwayat kasbon</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
            <a href="{{ route('admin.rekap.index') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--slate"><i class="bi bi-file-earmark-text"></i></div>
                <div class="ui-menu__text">
                    <strong>Laporan Rekap</strong>
                    <span>Validasi rekap harian outlet</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
            <a href="{{ route('admin.aipos.edit') }}" class="ui-menu__item">
                <div class="ui-menu__icon ui-icon--sky"><i class="bi bi-qr-code"></i></div>
                <div class="ui-menu__text">
                    <strong>Pengaturan AIPOS</strong>
                    <span>URL & kredensial login untuk pegawai</span>
                </div>
                <i class="bi bi-chevron-right ui-menu__chevron"></i>
            </a>
        </div>
    </section>
</div>
@endsection
