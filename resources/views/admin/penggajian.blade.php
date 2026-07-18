@extends('layouts.app')

@section('title', 'Penggajian Pegawai')

@section('back-button')
<a class="ui-back" href="javascript:history.back()"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Penggajian Pegawai</h1>
            <p>Pilih pegawai</p>
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
        <h2 class="ui-section__title">Daftar Pegawai</h2>

        @if ($pegawai->isNotEmpty())
            <div class="ui-menu">
                @foreach ($pegawai as $p)
                    <a href="{{ route('penggajian.show', $p->id) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--green"><i class="bi bi-cash-stack"></i></div>
                        <div class="ui-menu__text">
                            <strong>{{ $p->nama }}</strong>
                            <span>
                                {{ $p->username }}
                                · {{ $p->outlet->nama ?? 'Belum ditugaskan' }}
                                · Gaji Rp {{ number_format($p->gaji_harian, 0, ',', '.') }}
                            </span>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="ui-empty">
                <i class="bi bi-people"></i>
                <p>Belum ada pegawai terdaftar.</p>
                <a href="{{ route('pegawai.index') }}" class="ui-primary" style="max-width:280px;margin:1rem auto 0;display:flex;">
                    <div class="ui-primary__icon"><i class="bi bi-people"></i></div>
                    <div class="ui-primary__body">
                        <strong>Kelola Pegawai</strong>
                        <span>Tambah atau atur data pegawai</span>
                    </div>
                </a>
            </div>
        @endif
    </section>
</div>
@endsection
