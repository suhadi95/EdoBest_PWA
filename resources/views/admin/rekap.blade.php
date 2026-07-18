@extends('layouts.app')

@section('title', 'Laporan Rekap Harian')

@section('back-button')
<a class="ui-back" href="javascript:history.back()"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Laporan Rekap Harian</h1>
            <p>Pilih outlet</p>
        </div>
    </header>

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

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Outlet</h2>

        @if ($outlets->isNotEmpty())
            <div class="ui-menu">
                @foreach ($outlets as $outlet_item)
                    <a href="{{ route('admin.rekap.detail', $outlet_item->id) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--amber"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="ui-menu__text">
                            <strong>{{ $outlet_item->nama }}</strong>
                            <span>{{ $outlet_item->alamat }}</span>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="ui-empty">
                <i class="bi bi-shop"></i>
                <p>Belum ada outlet.</p>
            </div>
        @endif
    </section>
</div>
@endsection
