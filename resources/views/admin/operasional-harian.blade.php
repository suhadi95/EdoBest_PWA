@extends('layouts.app')

@section('title', 'Operasional Harian')

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Operasional Harian</h1>
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
                @foreach ($outlets as $outlet)
                    @php
                        $operasional = $outlet->operasionals->first();
                        $rekap = null;
                        if ($operasional) {
                            $rekap = $operasional->rekap;
                            if ($rekap && $rekap->status === 'validated') {
                                $statusText = 'Selesai';
                                $chipClass = 'ui-chip--violet';
                            } else {
                                $statusText = ucfirst($operasional->status);
                                $chipClass = $operasional->status === 'aktif' ? 'ui-chip--green' : 'ui-chip--rose';
                            }
                        } else {
                            $statusText = 'Tutup';
                            $chipClass = 'ui-chip--rose';
                        }
                        $cashInfo = $rekap ? ($rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0)) : null;
                    @endphp
                    <a href="{{ route('operasional.detail', $outlet->id) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--teal"><i class="bi bi-shop"></i></div>
                        <div class="ui-menu__text">
                            <strong>{{ $outlet->nama }}</strong>
                            <span>
                                {{ $outlet->alamat }}
                                · <span class="ui-chip {{ $chipClass }}">{{ $statusText }}</span>
                                @if($cashInfo !== null)
                                    · Cash di Pegawai: Rp {{ number_format($cashInfo, 0, ',', '.') }}
                                @endif
                            </span>
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
