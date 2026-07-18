@extends('layouts.app')

@section('title', 'Catatan Listrik')

@section('content')
@php
    $hariNama = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $bulanNama = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
@endphp

<div class="ui-page">
    <a href="{{ route('pegawai.dashboard') }}" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <header class="ui-header">
        <div>
            <h1>Catatan Listrik</h1>
            <p>{{ $outlet->nama }} · Rp {{ number_format($outlet->biaya_listrik_harian ?? 0, 0, ',', '.') }}/hari</p>
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

    <div class="ui-stats">
        <div class="ui-stat">
            <span>Hari belum dibayar</span>
            <strong>{{ $jumlahHari }}</strong>
        </div>
        <div class="ui-stat">
            <span>Total tagihan</span>
            <strong>Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
        </div>
    </div>

    @if ($jumlahHari > 0)
        <form action="{{ route('pegawai.listrik.bayar') }}" method="POST" onsubmit="return confirm('Catat pembayaran listrik sebesar Rp {{ number_format($totalTagihan, 0, ',', '.') }} untuk {{ $jumlahHari }} hari operasional?');">
            @csrf
            <button type="submit" class="ui-primary">
                <div class="ui-primary__icon"><i class="bi bi-cash-coin"></i></div>
                <div class="ui-primary__body">
                    <strong>Bayar Sekarang</strong>
                    <span>Petugas datang · catat pembayaran Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                </div>
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    @endif

    <section class="ui-section">
        <h2 class="ui-section__title">Operasional belum dibayar</h2>
        @if ($belumBayar->isNotEmpty())
            <div class="ui-menu">
                @foreach ($belumBayar as $op)
                    @php
                        $tgl = \Carbon\Carbon::parse($op->tanggal);
                    @endphp
                    <div class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--amber"><i class="bi bi-calendar3"></i></div>
                        <div class="ui-menu__text">
                            <strong>{{ $hariNama[$tgl->dayOfWeek] }}, {{ $tgl->day }} {{ $bulanNama[$tgl->month] }} {{ $tgl->year }}</strong>
                            <span>Biaya listrik hari itu</span>
                        </div>
                        <span class="ui-chip ui-chip--amber">Rp {{ number_format($op->biaya_listrik, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="ui-empty">
                <i class="bi bi-check-circle"></i>
                <p class="mb-0">Tidak ada tagihan listrik. Semua operasional sudah dibayar atau belum ada operasional.</p>
            </div>
        @endif
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Histori pembayaran</h2>
        @if ($histori->isNotEmpty())
            <div class="ui-menu">
                @foreach ($histori as $bayar)
                    @php
                        $dibayar = $bayar->dibayar_at;
                        $tanggalOps = $bayar->operasionals
                            ->sortBy('tanggal')
                            ->map(fn ($o) => \Carbon\Carbon::parse($o->tanggal)->format('d/m'))
                            ->values()
                            ->all();
                        $ringkasTanggal = count($tanggalOps) > 4
                            ? implode(', ', array_slice($tanggalOps, 0, 3)) . ', …'
                            : implode(', ', $tanggalOps);
                    @endphp
                    <div class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--green"><i class="bi bi-lightning-charge"></i></div>
                        <div class="ui-menu__text">
                            <strong>Rp {{ number_format($bayar->total_nominal, 0, ',', '.') }}</strong>
                            <span>
                                {{ $dibayar->format('d/m/Y H:i') }}
                                · {{ $bayar->jumlah_hari }} hari
                                @if ($ringkasTanggal)
                                    · {{ $ringkasTanggal }}
                                @endif
                                @if ($bayar->pegawai)
                                    · oleh {{ $bayar->pegawai->nama }}
                                @endif
                            </span>
                        </div>
                        <span class="ui-chip ui-chip--green">Lunas</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">
                {{ $histori->links() }}
            </div>
        @else
            <div class="ui-empty">
                <i class="bi bi-clock-history"></i>
                <p class="mb-0">Belum ada histori pembayaran listrik.</p>
            </div>
        @endif
    </section>
</div>
@endsection
