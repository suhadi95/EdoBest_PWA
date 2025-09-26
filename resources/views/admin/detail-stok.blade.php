@extends('layouts.app')

@section('title', 'Detail Stok Kemasan - {{ $outlet->nama }}')

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
                <h5 class="card-title"><i class="bi bi-box-seam me-2"></i>{{ $outlet->nama }}</h5>
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

                <!-- Stok Kemasan -->
                <h6>Stok Kemasan</h6>
                <p>Mika: {{ $stokOutlet->stok_mika }}</p>
                <p>Dus 1: {{ $stokOutlet->stok_dus1 }}</p>
                <p>Dus 2: {{ $stokOutlet->stok_dus2 }}</p>
                <p>Dus 3: {{ $stokOutlet->stok_dus3 }}</p>
                <p>Box: {{ $stokOutlet->stok_box }}</p>
                <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#stokModal">
                    <i class="bi bi-pencil-square me-2"></i>Update Stok Manual
                </button>

                <!-- Histori Stok -->
                <h6>Histori Stok</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Perubahan</th>
                                <th>Keterangan</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($historiStoks as $histori)
                                <tr>
                                    <td>{{ ucfirst($histori->jenis_stok) }}</td>
                                    <td>{{ $histori->jumlah_perubahan > 0 ? '+' : '' }}{{ $histori->jumlah_perubahan }}</td>
                                    <td>{{ $histori->keterangan }}</td>
                                    <td>{{ $histori->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada histori.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Stok Manual -->
<div class="modal fade" id="stokModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stok Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stok.update') }}" method="POST">
                @csrf
                <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jenis_stok" class="form-label">Jenis Stok</label>
                        <select class="form-select" name="jenis_stok" required>
                            <option value="mika">Mika</option>
                            <option value="dus1">Dus 1</option>
                            <option value="dus2">Dus 2</option>
                            <option value="dus3">Dus 3</option>
                            <option value="box">Box</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_perubahan" class="form-label">Jumlah Perubahan (positif untuk tambah, negatif untuk kurang)</label>
                        <input type="number" class="form-control" name="jumlah_perubahan" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection