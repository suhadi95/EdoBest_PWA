@extends('layouts.app')

@section('title', 'Detail Rekap Harian - {{ $outlet->nama }}')

@section('back-button')
    <a href="{{ route('pegawai.dashboard') }}" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page ui-page--wide">
    <header class="ui-header">
        <div>
            <h1>Detail Rekap Harian</h1>
            <p>{{ $outlet->nama }} · {{ $outlet->alamat }}</p>
        </div>
        <div class="ui-header__meta">
            <strong>{{ \Carbon\Carbon::parse($rekap->tanggal)->format('d-m-Y') }}</strong>
            Tanggal rekap
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
        <h2 class="ui-section__title">Informasi Rekap</h2>
        <div class="ui-stats">
            <div class="ui-stat">
                <span>Donat terjual</span>
                <strong>{{ $totalDonatTerjual }}</strong>
            </div>
            <div class="ui-stat">
                <span>Uang penjualan</span>
                <strong>Rp {{ number_format($rekap->total_uang_penjualan ?? $rekap->total_uang, 0, ',', '.') }}</strong>
            </div>
            <div class="ui-stat">
                <span>Total pendapatan</span>
                <strong>Rp {{ number_format($rekap->total_uang, 0, ',', '.') }}</strong>
            </div>
        </div>
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Transaksi</h2>
        <div class="ui-panel" style="padding:0; overflow:hidden;">
            <div class="table-responsive" style="border:none; border-radius:0;">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Waktu</th>
                            <th>Metode Pembayaran</th>
                            <th>Total Donat</th>
                            <th>Total Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekap->operasional->transaksis ?? [] as $index => $transaksi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($transaksi->created_at)->format('H:i:s') }}</td>
                                <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                                <td>{{ $transaksi->total_donat }}</td>
                                <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#transaksiModal{{ $transaksi->id }}">
                                        <i class="bi bi-list"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Sisa Produk</h2>
        <div class="ui-panel" style="padding:0; overflow:hidden;">
            <div class="table-responsive" style="border:none; border-radius:0;">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kemasan</th>
                            <th>Penggunaan</th>
                            <th>Sisa Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mika</td>
                            <td>{{ $usedMika }}</td>
                            <td>{{ $totalMika }}</td>
                        </tr>
                        <tr>
                            <td>Dus 1</td>
                            <td>{{ $usedDus1 }}</td>
                            <td>{{ $totalDus1 }}</td>
                        </tr>
                        <tr>
                            <td>Dus 2</td>
                            <td>{{ $usedDus2 }}</td>
                            <td>{{ $totalDus2 }}</td>
                        </tr>
                        <tr>
                            <td>Dus 3</td>
                            <td>{{ $usedDus3 }}</td>
                            <td>{{ $totalDus3 }}</td>
                        </tr>
                        <tr>
                            <td>Box</td>
                            <td>{{ $usedBox }}</td>
                            <td>{{ $totalBox }}</td>
                        </tr>
                        <tr>
                            <td>Box 12</td>
                            <td>{{ $usedBox12 }}</td>
                            <td>{{ $totalBox12 }}</td>
                        </tr>
                        <tr>
                            <td>Lilin</td>
                            <td>{{ $rekap->used_lilin ?? 0 }}</td>
                            <td>{{ $rekap->sisa_lilin ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Catatan Operasional</h2>
        <div class="ui-panel" style="padding:0; overflow:hidden;">
            <div class="table-responsive" style="border:none; border-radius:0;">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($catatanOperasionals as $index => $catatan)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ ucfirst($catatan->jenis) }}</td>
                                <td>Rp {{ number_format($catatan->jumlah, 0, ',', '.') }}</td>
                                <td>{{ $catatan->catatan ?? '-' }}
                                    {{ $catatan->kategori_kemasan ? "(Kemasan: " . ucfirst($catatan->kategori_kemasan) . ")" : '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada catatan operasional.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('pegawai.dashboard') }}" class="btn btn-primary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
        @if(($rekap->status ?? 'pending') === 'pending' && (session('user') && session('user')->id === $rekap->pegawai_id))
            <form action="{{ route('pegawai.rekap.delete', [$outlet->id, $rekap->id]) }}" method="POST"
                onsubmit="return confirm('Hapus rekap ini? Tindakan tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash3 me-1"></i>Hapus Rekap
                </button>
            </form>
        @endif
    </div>
</div>

    @forelse ($rekap->operasional->transaksis ?? [] as $transaksi)
        <div class="modal fade nested-modal" id="transaksiModal{{ $transaksi->id }}" tabindex="-1" aria-hidden="true"
            data-bs-backdrop="static" style="z-index: 1055;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Transaksi -
                            {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d-m-Y H:i:s') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->metode_pembayaran) }}</p>
                        <p><strong>Total Donat:</strong> {{ $transaksi->total_donat }}</p>
                        <p><strong>Total Harga:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                        <h6>Detail Items</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kemasan</th>
                                        <th>Jumlah</th>
                                        <th>Donat</th>
                                        <th>Jenis</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalJumlah = 0;
                                        $totalDonat = 0;
                                        $totalHarga = 0;
                                    @endphp
                                    @forelse ($transaksi->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ ucfirst($item->kemasan) }}</td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td>{{ $item->jumlah }}</td>
                                            <td>{{ ucfirst($item->tipe) }}</td>
                                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $totalJumlah += $item->jumlah;
                                            $totalDonat += $item->jumlah;
                                            $totalHarga += $item->subtotal;
                                        @endphp
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Belum ada item.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($transaksi->items->count() > 0)
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th>{{ $totalJumlah }}</th>
                                            <th>{{ $totalDonat }}</th>
                                            <th>-</th>
                                            <th>Rp {{ number_format($totalHarga, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @empty
    @endforelse
@endsection
