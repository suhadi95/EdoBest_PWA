@extends('layouts.app')

@section('title', 'Pilih Tanggal Rekap - {{ $outlet->nama }}')

@section('back-button')
<a href="{{ route('pegawai.dashboard') }}" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Pilih Tanggal Rekap</h1>
            <p>Operasional dengan transaksi yang belum direkap</p>
        </div>
        <div class="ui-header__meta">
            <strong>{{ $outlet->nama }}</strong>
            {{ $outlet->alamat }}
        </div>
    </header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="ui-panel" style="display:flex;align-items:flex-start;gap:0.75rem;">
        <div class="ui-menu__icon ui-icon--sky"><i class="bi bi-info-circle"></i></div>
        <div>
            <strong style="display:block;font-size:0.94rem;margin-bottom:0.15rem;">Informasi</strong>
            <span style="font-size:0.82rem;color:var(--muted);">
                Berikut daftar hari operasional yang memiliki transaksi namun belum memiliki laporan rekap. Pilih tanggal untuk membuat rekap yang terlewat.
            </span>
        </div>
    </div>

    <section class="ui-section">
        <h2 class="ui-section__title">Tanggal Operasional</h2>

        @if ($operasionalsWithoutRekap->count() > 0)
            <div class="ui-menu">
                @foreach ($operasionalsWithoutRekap as $operasional)
                    @php
                        $transaksiCount = \App\Models\Transaksi::where('operasional_id', $operasional->id)->count();
                    @endphp
                    <a href="{{ route('pegawai.rekap', ['outlet_id' => $outlet->id, 'tanggal' => $operasional->tanggal]) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--amber"><i class="bi bi-calendar3"></i></div>
                        <div class="ui-menu__text">
                            <strong>{{ \Carbon\Carbon::parse($operasional->tanggal)->format('d F Y') }}</strong>
                            <span>
                                {{ \Carbon\Carbon::parse($operasional->tanggal)->diffForHumans() }}
                                · {{ $transaksiCount }} transaksi
                            </span>
                            <div class="mt-1">
                                <span class="ui-chip {{ $operasional->status === 'aktif' ? 'ui-chip--green' : '' }}">{{ ucfirst($operasional->status) }}</span>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="ui-panel">
                <div class="ui-empty">
                    <i class="bi bi-check-circle"></i>
                    <p class="mb-2">Semua rekap sudah dibuat. Tidak ada operasional yang memerlukan laporan rekap saat ini.</p>
                    <a href="{{ route('pegawai.dashboard') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-house-door me-1"></i>Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
