@extends('layouts.app')

@section('title', 'Operasional Harian')

@section('css')
<style>
    .card-body p {
        margin-bottom: 10px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-shop me-2"></i>Operasional Harian</h5>
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

                <h6>Pilih Outlet</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Outlet</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($outlets as $index => $outlet_item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $outlet_item->nama }}</td>
                                    <td>{{ $outlet_item->alamat }}</td>
                                    <td>
                                        @php
                                            $operasional = $outlet_item->operasionals->first();
                                            if ($operasional) {
                                                $rekap = $operasional->rekap;
                                                if ($rekap && $rekap->status === 'validated') {
                                                    $statusText = 'Selesai';
                                                    $badgeClass = 'primary';
                                                } else {
                                                    $statusText = ucfirst($operasional->status);
                                                    $badgeClass = $operasional->status === 'aktif' ? 'success' : 'danger';
                                                }
                                            } else {
                                                $statusText = 'Tutup';
                                                $badgeClass = 'danger';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                                        @if(isset($rekap))
                                        @php $cashBrief = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0); @endphp
                                        <div class="small mt-1">Cash: Rp {{ number_format($cashBrief, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('operasional.detail', $outlet_item->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Detail Operasional
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada outlet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection