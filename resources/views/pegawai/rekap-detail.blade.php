@extends('layouts.app')

@section('title', 'Detail Rekap Harian - {{ $outlet->nama }}')

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
                <h5 class="card-title"><i class="bi bi-file-text me-2"></i>Detail Rekap Harian - {{ $outlet->nama }}</h5>
                <p>Alamat: {{ $outlet->alamat }}</p>
                <p>Tanggal: {{ \Carbon\Carbon::parse($rekap->tanggal)->format('d-m-Y') }}</p>
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

                <h6>Informasi Rekap</h6>
                <p>Total Donat Terjual: {{ $totalDonatTerjual }}</p>
                <p>Sisa Stok Kemasan:</p>
                <ul>
                    <li>Mika: {{ $totalMika }}</li>
                    <li>Dus 1: {{ $totalDus1 }}</li>
                    <li>Dus 2: {{ $totalDus2 }}</li>
                    <li>Dus 3: {{ $totalDus3 }}</li>
                    <li>Box: {{ $totalBox }}</li>
                </ul>
                <p>Total Uang: Rp {{ number_format($totalUang, 0, ',', '.') }}</p>

                <h6>Catatan Operasional</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
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
                                    <td>{{ $catatan->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada catatan operasional.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('pegawai.penjualan', $outlet->id) }}" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Penjualan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection