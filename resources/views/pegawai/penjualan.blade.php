@extends('layouts.app')

@section('title', 'Penjualan - {{ $outlet->nama }}')

@section('css')
<style>
    .card-body p {
        margin-bottom: 10px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cart me-2"></i>Penjualan - {{ $outlet->nama }}</h5>
                <p>Alamat: {{ $outlet->alamat }}</p>
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

                @if ($operasional)
                    <p>Tanggal: {{ \Carbon\Carbon::today()->format('d-m-Y') }}</p>
                    <p>Total Kloter: {{ $totalKloter }}</p>
                    <p>Total Donat Tersedia: {{ $operasional->total_donat_harian }}</p>
                    <h6>Stok Kemasan</h6>
                    <p>Mika: {{ $totalMika }}</p>
                    <p>Dus 1: {{ $totalDus1 }}</p>
                    <p>Dus 2: {{ $totalDus2 }}</p>
                    <p>Dus 3: {{ $totalDus3 }}</p>
                    <p>Box: {{ $totalBox }}</p>

                    <h6>Daftar Transaksi</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Donat</th>
                                    <th>Total</th>
                                    <th>Pembayaran</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksis as $index => $transaksi)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $transaksi->total_donat }}</td>
                                        <td>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($transaksi->metode_pembayaran) }}</td>
                                        <td>{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('pegawai.transaksi.detail', [$outlet->id, $transaksi->id]) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada transaksi hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('pegawai.transaksi', $outlet->id) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Transaksi
                        </a>
                        @if ($operasional->status === 'aktif')
                            <a href="{{ route('pegawai.rekap', $outlet->id) }}" class="btn btn-primary">
                                <i class="bi bi-file-text me-2"></i>Buat Rekap Harian
                            </a>
                        @endif
                    </div>
                @else
                    <div class="alert alert-info">Operasional belum dimulai hari ini.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection