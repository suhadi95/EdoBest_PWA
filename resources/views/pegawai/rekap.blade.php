@extends('layouts.app')

@section('title', 'Rekap Harian - {{ $outlet->nama }}')

@section('css')
<style>
    .card-body p {
        margin-bottom: 10px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .form-check-label {
        margin-right: 15px;
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-text me-2"></i>Rekap Harian - {{ $outlet->nama }}</h5>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tempCatatan as $index => $catatan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ ucfirst($catatan['jenis']) }}</td>
                                    <td>Rp {{ number_format($catatan['jumlah'], 0, ',', '.') }}</td>
                                    <td>{{ $catatan['catatan'] ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger" onclick="hapusCatatan({{ $index }})">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada catatan operasional.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#catatanModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Catatan
                </button>

                <form action="{{ route('pegawai.simpan-rekap') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    <input type="hidden" name="operasional_id" value="{{ $operasional->id }}">
                    <button type="submit" class="btn btn-success">Simpan Laporan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Catatan -->
<div class="modal fade" id="catatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Catatan Operasional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="catatanForm" action="{{ route('pegawai.tambah-catatan') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis" value="pemasukkan" checked required>
                                <label class="form-check-label">Pemasukkan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis" value="pengeluaran">
                                <label class="form-check-label">Pengeluaran</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control" name="jumlah" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" name="catatan" rows="4"></textarea>
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
@endsection

@section('js')
<script>
    document.getElementById('catatanForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route('pegawai.tambah-catatan') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(JSON.stringify(err.error)) });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('catatanModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Gagal menambahkan catatan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Gagal menambahkan catatan';
            try {
                const parsedError = JSON.parse(error.message);
                errorMessage = Object.values(parsedError).flat().join(', ');
            } catch (e) {
                errorMessage = error.message;
            }
            alert('Gagal menambahkan catatan: ' + errorMessage);
        });
    });

    function hapusCatatan(index) {
        if (confirm('Hapus catatan ini?')) {
            fetch('{{ route('pegawai.hapus-catatan') }}', {
                method: 'POST',
                body: JSON.stringify({ index: index }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error || 'Gagal menghapus catatan') });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Gagal menghapus catatan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus catatan: ' + error.message);
            });
        }
    }
</script>
@endsection