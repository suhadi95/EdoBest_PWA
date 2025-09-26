@extends('layouts.app')

@section('title', 'Detail Operasional - {{ $outlet->nama }}')

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
                <h5 class="card-title"><i class="bi bi-shop me-2"></i>{{ $outlet->nama }}</h5>
                <p>Alamat: {{ $outlet->alamat }}</p>
                <p>Tanggal: {{ \Carbon\Carbon::today()->format('d-m-Y') }}</p>
                
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

                @if (!$operasional)
                    <!-- Operasional belum dimulai -->
                    <div class="alert alert-info">Operasional belum dimulai hari ini.</div>
                    <form action="{{ route('operasional.mulai') }}" method="POST">
                        @csrf
                        <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                        <button type="submit" class="btn btn-primary">Mulai Operasional</button>
                    </form>
                @else
                    <!-- Operasional sudah aktif -->
                    <p>Status: <span class="badge bg-{{ $operasional->status === 'aktif' ? 'success' : 'danger' }}">{{ ucfirst($operasional->status) }}</span></p>
                    <p>Total Donat Hari Ini: {{ $operasional->total_donat_harian }}</p>
                    
                    <!-- Tambah Kloter -->
                    @if ($operasional->status === 'aktif')
                        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#kloterModal">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Kloter
                        </button>
                    @endif

                    <!-- Tabel Kloter Hari Ini -->
                    <h6>Kloter Hari Ini</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Donat</th>
                                    <th>Mika</th>
                                    <th>Dus 1</th>
                                    <th>Dus 2</th>
                                    <th>Dus 3</th>
                                    <th>Box</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kloters as $kloter)
                                    <tr>
                                        <td>{{ $kloter->jumlah_donat }}</td>
                                        <td>{{ $kloter->jumlah_mika }}</td>
                                        <td>{{ $kloter->jumlah_dus1 }}</td>
                                        <td>{{ $kloter->jumlah_dus2 }}</td>
                                        <td>{{ $kloter->jumlah_dus3 }}</td>
                                        <td>{{ $kloter->jumlah_box }}</td>
                                        <td>{{ $kloter->created_at->format('H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada kloter hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Sisa Donat dan Kemasan -->
                    <h6>Sisa Donat dan Kemasan</h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Donat</th>
                                    <th>Mika</th>
                                    <th>Dus 1</th>
                                    <th>Dus 2</th>
                                    <th>Dus 3</th>
                                    <th>Box</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $operasional->total_donat_harian }}</td>
                                    <td>{{ $totalMika }}</td>
                                    <td>{{ $totalDus1 }}</td>
                                    <td>{{ $totalDus2 }}</td>
                                    <td>{{ $totalDus3 }}</td>
                                    <td>{{ $totalBox }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Detail Rekap -->
                    <h6>Rekap Harian</h6>
                    @if ($rekap)
                        <a href="{{ route('admin.rekap.detail', [$outlet->id]) }}" class="btn btn-primary mb-3">
                            <i class="bi bi-eye me-2"></i>Detail Rekap
                        </a>
                    @else
                        <p class="text-muted">Rekap belum dibuat oleh pegawai.</p>
                    @endif

                    <!-- Tombol Validasi Operasional -->
                    @if ($operasional->status === 'aktif' && $rekap)
                        <form action="{{ route('admin.rekap.validasi', [$outlet->id, $rekap->id]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success mb-3">
                                <i class="bi bi-check-circle me-2"></i>Validasi Operasional
                            </button>
                        </form>
                    @elseif ($operasional->status === 'aktif')
                        <p class="text-muted">Rekap belum dibuat oleh pegawai, tidak bisa divalidasi.</p>
                    @else
                        <p class="text-muted">Operasional sudah divalidasi.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Kloter -->
@if ($operasional)
<div class="modal fade" id="kloterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kloter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('operasional.kloter.store') }}" method="POST">
                @csrf
                <input type="hidden" name="operasional_id" value="{{ $operasional->id }}">
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="jumlah_donat" class="form-label">Jumlah Donat</label>
                        <input type="number" class="form-control" name="jumlah_donat" min="0" value="{{ old('jumlah_donat') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_mika" class="form-label">Jumlah Mika</label>
                        <input type="number" class="form-control" name="jumlah_mika" min="0" value="{{ old('jumlah_mika') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_dus1" class="form-label">Jumlah Dus 1</label>
                        <input type="number" class="form-control" name="jumlah_dus1" min="0" value="{{ old('jumlah_dus1') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_dus2" class="form-label">Jumlah Dus 2</label>
                        <input type="number" class="form-control" name="jumlah_dus2" min="0" value="{{ old('jumlah_dus2') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_dus3" class="form-label">Jumlah Dus 3</label>
                        <input type="number" class="form-control" name="jumlah_dus3" min="0" value="{{ old('jumlah_dus3') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_box" class="form-label">Jumlah Box</label>
                        <input type="number" class="form-control" name="jumlah_box" min="0" value="{{ old('jumlah_box') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection