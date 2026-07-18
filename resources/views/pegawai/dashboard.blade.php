@extends('layouts.app')

@section('title', 'Dashboard Pegawai')

@section('content')
@php
    $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][now()->dayOfWeek];
    $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][now()->month];
    $operasional = $outlet?->operasionals->first();
    $rekapSudahAda = false;
    $isSelesai = false;
    $statusText = 'Tutup';
    $chipClass = 'ui-chip--rose';

    if ($operasional) {
        $rekap = $operasional->rekap;
        if ($rekap) {
            $rekapSudahAda = true;
            if ($rekap->status === 'validated') {
                $statusText = 'Selesai';
                $chipClass = 'ui-chip--violet';
                $isSelesai = true;
            } else {
                $statusText = 'Menunggu Validasi';
                $chipClass = 'ui-chip--amber';
            }
        } else {
            $statusText = ucfirst($operasional->status);
            $chipClass = $operasional->status === 'aktif' ? 'ui-chip--green' : 'ui-chip--rose';
        }
    }

    $hasOperasionalWithoutRekap = false;
    if ($outlet) {
        $hasOperasionalWithoutRekap = \App\Models\Operasional::where('outlet_id', $outlet->id)
            ->where('tanggal', '>=', \Carbon\Carbon::now()->subDays(30)->toDateString())
            ->whereDoesntHave('rekap')
            ->whereHas('transaksis')
            ->whereIn('status', ['aktif', 'selesai'])
            ->exists();
    }
@endphp

<div class="ui-page">
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

    <header class="ui-header">
        <div>
            <h1>Halo, {{ session('user')->nama }}</h1>
            <p>Ringkasan kerja outlet hari ini</p>
        </div>
        <div class="ui-header__meta">
            <strong>{{ $hari }}</strong>
            {{ now()->day }} {{ $bulan }} {{ now()->year }}
        </div>
    </header>

    @if ($outlet)
        <div class="ui-panel" style="display:flex;align-items:center;gap:0.9rem;">
            <div class="ui-menu__icon ui-icon--teal"><i class="bi bi-shop"></i></div>
            <div style="flex:1;min-width:0;">
                <strong style="display:block;font-size:1rem;">{{ $outlet->nama }}</strong>
                <span style="font-size:0.82rem;color:var(--muted);"><i class="bi bi-geo-alt me-1"></i>{{ $outlet->alamat }}</span>
            </div>
            <span class="ui-chip {{ $chipClass }}">{{ $statusText }}</span>
        </div>

        @if ($operasional && $operasional->status === 'aktif')
            <a href="{{ route('pegawai.penjualan', $outlet->id) }}" class="ui-primary">
                <div class="ui-primary__icon"><i class="bi bi-cart-plus"></i></div>
                <div class="ui-primary__body">
                    <strong>Mulai Penjualan</strong>
                    <span>Catat transaksi pelanggan hari ini</span>
                </div>
                <i class="bi bi-arrow-right"></i>
            </a>
        @endif

        <section class="ui-section">
            <h2 class="ui-section__title">Aksi Cepat</h2>
            <div class="ui-menu">
                @if ($operasional && $operasional->status === 'aktif' && !$rekapSudahAda)
                    <a href="{{ route('pegawai.rekap', $outlet->id) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--amber"><i class="bi bi-clipboard-check"></i></div>
                        <div class="ui-menu__text">
                            <strong>Buat Rekap</strong>
                            <span>Rekap operasional hari ini</span>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @else
                    <a href="{{ route('pegawai.histori-rekap.index') }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--violet"><i class="bi bi-clock-history"></i></div>
                        <div class="ui-menu__text">
                            <strong>Histori Rekap</strong>
                            <span>Lihat rekap yang sudah dibuat</span>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @endif

                @if ($hasOperasionalWithoutRekap)
                    <a href="{{ route('pegawai.pilih-tanggal-rekap', $outlet->id) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--rose"><i class="bi bi-calendar-plus"></i></div>
                        <div class="ui-menu__text">
                            <strong>Buat Rekap Lama</strong>
                            <span>Ada operasional yang belum direkap</span>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @endif

                <a href="{{ route('pegawai.kasbon.index') }}" class="ui-menu__item">
                    <div class="ui-menu__icon ui-icon--sky"><i class="bi bi-wallet2"></i></div>
                    <div class="ui-menu__text">
                        <strong>Ajukan Kasbon</strong>
                        <span>Pengajuan & status kasbon</span>
                    </div>
                    <i class="bi bi-chevron-right ui-menu__chevron"></i>
                </a>

                <a href="{{ route('pegawai.listrik.index') }}" class="ui-menu__item">
                    <div class="ui-menu__icon ui-icon--amber" style="position:relative;">
                        <i class="bi bi-lightning-charge"></i>
                        @if ($listrikHariBelumBayar > 0)
                            <span style="position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;padding:0 4px;border-radius:999px;background:#e11d48;color:#fff;font-size:0.65rem;font-weight:700;display:flex;align-items:center;justify-content:center;line-height:1;">{{ $listrikHariBelumBayar > 99 ? '99+' : $listrikHariBelumBayar }}</span>
                        @endif
                    </div>
                    <div class="ui-menu__text">
                        <strong>Catatan Listrik</strong>
                        @if ($listrikHariBelumBayar > 0)
                            <span>{{ $listrikHariBelumBayar }} hari belum dibayar</span>
                        @else
                            <span>Tidak ada tagihan aktif</span>
                        @endif
                    </div>
                    @if ($listrikHariBelumBayar > 0)
                        <span class="ui-chip ui-chip--rose">Rp {{ number_format($listrikTotalTagihan, 0, ',', '.') }}</span>
                    @else
                        <span class="ui-chip ui-chip--green">Lunas</span>
                    @endif
                </a>

                <a href="{{ route('pegawai.histori-gaji.index') }}" class="ui-menu__item">
                    <div class="ui-menu__icon ui-icon--green"><i class="bi bi-receipt"></i></div>
                    <div class="ui-menu__text">
                        <strong>Histori Gaji</strong>
                        <span>Lihat rincian gaji Anda</span>
                    </div>
                    <i class="bi bi-chevron-right ui-menu__chevron"></i>
                </a>
            </div>
        </section>
    @else
        <div class="ui-empty">
            <i class="bi bi-exclamation-triangle"></i>
            <p class="mb-0">Anda belum ditugaskan ke outlet mana pun.</p>
        </div>
    @endif
</div>
@endsection
